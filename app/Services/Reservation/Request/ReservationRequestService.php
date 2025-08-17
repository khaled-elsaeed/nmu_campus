<?php

namespace App\Services\Reservation\Request;

use App\Models\Reservation\ReservationRequest;
use App\Models\User;
use App\Models\Resident\Student;
use App\Models\Reservation\Accommodation;
use App\Models\Academic\AcademicTerm;
use App\Models\Housing\Apartment;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Services\Reservation\Operation\AcceptReservationRequestService;

class ReservationRequestService
{
    public function __construct(
        protected AcceptReservationRequestService $acceptReservationRequestService
    ) {}

    /**
     * Update an existing reservation request.
     *
     * @param ReservationRequest $reservationRequest
     * @param array $data
     * @return ReservationRequest
     */
    public function updateReservation(ReservationRequest $reservationRequest, array $data): ReservationRequest
    {
        return DB::transaction(function () use ($reservationRequest, $data) {
            if (isset($data['period_type'])) {
                if ($data['period_type'] === 'academic') {
                    $data['academic_term_id'] = null;
                } elseif ($data['period_type'] === 'calendar') {
                    $data['check_in_date'] = null;
                    $data['check_out_date'] = null;
                }
            }
            $reservationRequest->update($data);
            return $reservationRequest->fresh(['user', 'academicTerm', 'reviewer', 'createdReservation']);
        });
    }

    /**
     * Get a single reservation request with relationships.
     *
     * @param int $id
     * @return ReservationRequest
     */
    public function getReservation($id): ReservationRequest
    {
        return ReservationRequest::with(['user', 'academicTerm', 'reviewer', 'createdReservation'])->findOrFail($id);
    }


    /**
     * Get all reservation requests (for dropdowns/forms).
     *
     * @return array
     */
    public function getAll(): array
    {
        return ReservationRequest::with(['user', 'academicTerm', 'requestedAccommodation'])
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'request_number' => $request->request_number,
                    'user_name' => $request->user?->name_en ?? 'N/A',
                    'status' => $request->status,
                    'total_points' => $request->total_points,
                    'active' => $request->status === 'approved',
                ];
            })->toArray();
    }

    /**
     * Get reservation request statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $stats = ReservationRequest::leftJoin('users', 'reservation_requests.user_id', '=', 'users.id')
            ->selectRaw("
                COUNT(*) as total,
                MAX(reservation_requests.updated_at) as last_update,

                SUM(CASE WHEN reservation_requests.status = 'pending' THEN 1 ELSE 0 END) as pending,
                MAX(CASE WHEN reservation_requests.status = 'pending' THEN reservation_requests.updated_at ELSE NULL END) as pending_last_update,
                SUM(CASE WHEN reservation_requests.status = 'pending' AND users.gender = 'male' THEN 1 ELSE 0 END) as pending_male,
                SUM(CASE WHEN reservation_requests.status = 'pending' AND users.gender = 'female' THEN 1 ELSE 0 END) as pending_female,

                SUM(CASE WHEN reservation_requests.status = 'approved' THEN 1 ELSE 0 END) as approved,
                MAX(CASE WHEN reservation_requests.status = 'approved' THEN reservation_requests.updated_at ELSE NULL END) as approved_last_update,
                SUM(CASE WHEN reservation_requests.status = 'approved' AND users.gender = 'male' THEN 1 ELSE 0 END) as approved_male,
                SUM(CASE WHEN reservation_requests.status = 'approved' AND users.gender = 'female' THEN 1 ELSE 0 END) as approved_female,

                SUM(CASE WHEN reservation_requests.status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                MAX(CASE WHEN reservation_requests.status = 'rejected' THEN reservation_requests.updated_at ELSE NULL END) as rejected_last_update,
                SUM(CASE WHEN reservation_requests.status = 'rejected' AND users.gender = 'male' THEN 1 ELSE 0 END) as rejected_male,
                SUM(CASE WHEN reservation_requests.status = 'rejected' AND users.gender = 'female' THEN 1 ELSE 0 END) as rejected_female,

                SUM(CASE WHEN reservation_requests.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                MAX(CASE WHEN reservation_requests.status = 'cancelled' THEN reservation_requests.updated_at ELSE NULL END) as cancelled_last_update,
                SUM(CASE WHEN reservation_requests.status = 'cancelled' AND users.gender = 'male' THEN 1 ELSE 0 END) as cancelled_male,
                SUM(CASE WHEN reservation_requests.status = 'cancelled' AND users.gender = 'female' THEN 1 ELSE 0 END) as cancelled_female,

                SUM(CASE WHEN users.gender = 'male' THEN 1 ELSE 0 END) as total_male,
                SUM(CASE WHEN users.gender = 'female' THEN 1 ELSE 0 END) as total_female
            ")
            ->first(); // Add this to execute the query and get a single result

        return [
            'requests' => [
                'count' => formatNumber($stats->total),
                'male' => formatNumber($stats->total_male),
                'female' => formatNumber($stats->total_female),
                'lastUpdateTime' => formatDate($stats->last_update)
            ],
            'requests-pending' => [
                'count' => formatNumber($stats->pending),
                'male' => formatNumber($stats->pending_male),
                'female' => formatNumber($stats->pending_female),
                'lastUpdateTime' => formatDate($stats->pending_last_update)
            ],
            'requests-approved' => [
                'count' => formatNumber($stats->approved),
                'male' => formatNumber($stats->approved_male),
                'female' => formatNumber($stats->approved_female),
                'lastUpdateTime' => formatDate($stats->approved_last_update)
            ],
        ];
    }

    /**
     * Get reservation request data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $query = ReservationRequest::with(['user', 'academicTerm']);
        $query = $this->applySearchFilters($query);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('request_number', fn($request) => $request->request_number)
            ->addColumn('name', fn($request) => $request->user?->name ?? 'N/A')
            ->addColumn('period', fn($request) => $this->getPeriod($request))
            ->addColumn('status', fn($request) => ucfirst($request->status))
            ->addColumn('total_points', fn($request) => $request->total_points)
            ->addColumn('created_at', fn($request) => formatDate($request->created_at))
            ->addColumn('action', fn($request) => $this->renderActionButtons($request))

            ->orderColumn('request_number', 'reservation_requests.request_number $1')
            ->orderColumn('user_name', function ($query, $order) {
                return $query->leftJoin('users', 'reservation_requests.user_id', '=', 'users.id')
                             ->orderBy('users.name_en', $order);
            })
            ->orderColumn('academic_term', function ($query, $order) {
                return $query->leftJoin('academic_terms', 'reservation_requests.academic_term_id', '=', 'academic_terms.id')
                             ->orderBy('academic_terms.name_en', $order);
            })
            ->orderColumn('status', 'reservation_requests.status $1')
            ->orderColumn('total_points', 'reservation_requests.total_points $1')
            ->orderColumn('created_at', 'reservation_requests.created_at $1')

            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Apply search filters to the query.
     *
     * @param Builder $query
     * @return Builder
     */
    protected function applySearchFilters($query): Builder
    {
        if (request()->filled('search_request_number') && !empty(request('search_request_number'))) {
            $search = mb_strtolower(request('search_request_number'));
            $query->whereRaw('LOWER(request_number) LIKE ?', ['%' . $search . '%']);
        }

        if (request()->filled('search_user_name') && !empty(request('search_user_name'))) {
            $search = mb_strtolower(request('search_user_name'));
            $query->whereHas('user', function ($q) use ($search) {
                $q->whereRaw('LOWER(name_en) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(name_ar) LIKE ?', ['%' . $search . '%']);
            });
        }

        if (request()->filled('search_status')) {
            $query->where('status', request('search_status'));
        }

        if (request()->filled('search_academic_term_id')) {
            $query->where('academic_term_id', request('search_academic_term_id'));
        }

        if (request()->filled('search_accommodation_type')) {
            $query->where('accommodation_type', request('search_accommodation_type'));
        }

        return $query;
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param ReservationRequest $request
     * @return string
     */
    protected function renderActionButtons($request): string
    {
        $actions = ['view', 'edit'];
        $singleActions = [];

        if ($request->status === 'pending') {
            $singleActions[] = [
                'action' => 'accept',
                'icon' => 'bx bx-check',
                'class' => 'btn-success',
                'label' => __('Accept'),
                'data' => [
                    'accommodation-type' => $request->accommodation_type,
                    'bed-count' => $request->bed_count,
                ],
                'modal_toggle' => 'modal',
                'modal_target' => '#acceptRequestModal'
            ];
            $singleActions[] = [
                'action' => 'reject',
                'icon' => 'bx bx-x',
                'class' => 'btn-danger',
                'label' => __('Reject')
            ];
        }

        return view('components.ui.datatable.table-actions', [
            'mode' => 'both',
            'actions' => $actions,
            'id' => $request->id,
            'type' => 'ReservationRequest',
            'singleActions' => $singleActions
        ])->render();
    }

        /**
     * Get period information for display.
     *
     * @param ReservationRequest $request
     * @return string
     */
    private function getPeriod(ReservationRequest $request): string
    {
        $period = 'N/A';

        switch($request->period_type) {
            case 'academic':
                $period = $request->academicTerm->name ?? 'N/A';
                break;
            case 'calendar':
                if ($request->check_in_date && $request->check_out_date) {
                    $checkInDate = formatDate($request->check_in_date);
                    $checkOutDate = formatDate($request->check_out_date);
                    $period = $checkInDate . ' - ' . $checkOutDate;
                }
                break;
        }

        return $period;
    }

    /**
     * Get accommodation information for display.
     *
     * @param ReservationRequest $request
     * @return string
     */
    private function getAccommodationInfo(ReservationRequest $request): string
    {
        if ($request->accommodation_type === 'apartment') {
            return 'Apartment';
        }
        if ($request->accommodation_type === 'room') {
            if ($request->room_type === 'double') {
                $bedOption = $request->double_room_bed_option ? (', Bed: ' . $request->double_room_bed_option) : '';
                return 'Double Room' . $bedOption;
            }
            if ($request->room_type === 'single') {
                return 'Single Room';
            }
        }
        return 'N/A';
    }

    /**
     * Show a single reservation request (for controller show method).
     *
     * @param int $id
     * @return ReservationRequest|null
     */
    public function show($id)
    {
        return ReservationRequest::with(['user', 'academicTerm', 'reviewer', 'createdReservation'])->find($id);
    }

    /**
     * Get a single reservation request (for controller show method).
     *
     * @param int $id
     * @return array
     */
    public function getRequest(int $id): array
    {
        $request = ReservationRequest::select([
            'id',
            'student_id',
            'academic_term_id',
            'room_id',
            'status'
        ])->find($id);

        if (!$request) {
            throw new BusinessValidationException(__('Reservation request not found.'));
        }

        return [
            'id' => $request->id,
            'student_id' => $request->student_id,
            'academic_term_id' => $request->academic_term_id,
            'room_id' => $request->room_id,
            'status' => $request->status,
        ];
    }

    public function acceptRequest($data, $id): bool
    {
        $request = ReservationRequest::find($id);
        
        if (!$request) {
            throw new BusinessValidationException(__('Reservation request not found.'));
        }

        $reservation = $this->acceptReservationRequestService->accept(
            reservationRequestId: $request->id,
            accommodationType: $data['accommodation_type'],
            roomId: $data['accommodation_type'] === 'room' ? $data['accommodation_id'] : null,
            apartmentId: $data['accommodation_type'] === 'apartment' ? $data['accommodation_id'] : null,
            bedCount: $data['bed_count'] ?? null,
            notes: $data['notes'] ?? null
        );

        $request->update([
            'status' => 'approved',
            'created_reservation_id' => $reservation->id,
            'reviewer_id' => auth()->id(),
        ]);
    }

    public function cancelRequest($id): bool
    {
        $request = ReservationRequest::find($id);
        if (!$request) {
            throw new BusinessValidationException(__('Reservation request not found.'));
        }

        $request->status = 'canceled';
        return $request->save();
    }
} 