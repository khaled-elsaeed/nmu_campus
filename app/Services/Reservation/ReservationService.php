<?php

namespace App\Services\Reservation;

use App\Models\Reservation\Reservation;
use App\Models\User;
use App\Models\Resident\Student;
use App\Models\Reservation\Accommodation;
use App\Models\Academic\AcademicTerm;
use App\Models\Housing\Room;
use App\Models\Housing\Apartment;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Services\Reservation\CreateReservationService;
use Carbon\Carbon;

class ReservationService
{
    public function __construct(protected CreateReservationService $createReservationService)
    {}

    /**
     * Create a new reservation.
     *
     * @param array $data
     * @return Reservation|array
     */
    public function createReservation(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->createReservationService->create($data);
        });
    }

    /**
     * Cancel a reservation.
     *
     * @param int $reservationId
     * @return Reservation
     * @throws BusinessValidationException
     */
    public function cancel(int $reservationId): Reservation
    {
        $reservation = Reservation::findOrFail($reservationId);

        if ($reservation->status === 'checked_in') {
            throw new BusinessValidationException('Cannot cancel a reservation that has been checked in.');
        }

        if ($reservation->status === 'cancelled') {
            throw new BusinessValidationException('Reservation is already cancelled.');
        }

        $reservation->status = 'cancelled';
        $reservation->cancelled_at = Carbon::now();
        $reservation->save();

        return $reservation;
    }

    /**
     * Delete a reservation.
     *
     * @param int $reservationId
     * @return bool
     * @throws BusinessValidationException
     */
    public function deleteReservation(int $reservationId): bool
    {
        $reservation = Reservation::findOrFail($reservationId);

        if ($reservation->status === 'checked_in') {
            throw new BusinessValidationException('Cannot delete a reservation that has been checked in.');
        }

        $deleted = $reservation->delete();

        return $deleted;
    }

    /**
     * Get all reservations (for dropdowns/forms).
     *
     * @return array
     */
    public function getAll(): array
    {
        return Reservation::select(['id', 'student_id', 'room_id', 'status'])->get()->toArray();
    }

    /**
     * Get reservation statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $totalReservations = Reservation::count();
        $activeReservations = Reservation::where('active', true)->count();
        $inactiveReservations = Reservation::where('active', false)->count();
        $pendingReservations = Reservation::where('status', 'pending')->count();
        $confirmedReservations = Reservation::where('status', 'confirmed')->count();
        $checkedInReservations = Reservation::where('status', 'checked_in')->count();
        $checkedOutReservations = Reservation::where('status', 'checked_out')->count();
        $cancelledReservations = Reservation::where('status', 'cancelled')->count();
        
        $lastUpdateTime = formatDate(Reservation::max('updated_at'));
        $activeLastUpdate = formatDate(Reservation::where('active', true)->max('updated_at'));
        $inactiveLastUpdate = formatDate(Reservation::where('active', false)->max('updated_at'));
        
        return [
            'total' => [
                'count' => formatNumber($totalReservations),
                'lastUpdateTime' => $lastUpdateTime
            ],
            'active' => [
                'count' => formatNumber($activeReservations),
                'lastUpdateTime' => $activeLastUpdate
            ],
            'inactive' => [
                'count' => formatNumber($inactiveReservations),
                'lastUpdateTime' => $inactiveLastUpdate
            ],
            'statuses' => [
                'pending' => formatNumber($pendingReservations),
                'confirmed' => formatNumber($confirmedReservations),
                'checked_in' => formatNumber($checkedInReservations),
                'checked_out' => formatNumber($checkedOutReservations),
                'cancelled' => formatNumber($cancelledReservations),
            ]
        ];
    }

    /**
     * Get reservation data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $query = Reservation::with(['user', 'accommodation', 'academicTerm']);
        $query = $this->applySearchFilters($query);
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('reservation_number', fn($reservation) => $reservation->reservation_number)
            ->addColumn('name', fn($reservation) => $reservation->user?->name ?? 'N/A')
            ->addColumn('accommodation_info', fn($reservation) => $this->getAccommodationInfo($reservation))
            ->addColumn('academic_term', fn($reservation) => $reservation->academicTerm?->name ?? 'N/A')
            ->addColumn('check_in_date', fn($reservation) => $reservation->check_in_date ? formatDate($reservation->check_in_date) : 'N/A')
            ->addColumn('check_out_date', fn($reservation) => $reservation->check_out_date ? formatDate($reservation->check_out_date) : 'N/A')
            ->addColumn('status', fn($reservation) => ucfirst($reservation->status))
            ->addColumn('active', fn($reservation) => $reservation->active ? 'Active' : 'Inactive')
            ->addColumn('created_at', fn($reservation) => formatDate($reservation->created_at))
            ->addColumn('action', fn($reservation) => $this->renderActionButtons($reservation))                       
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
        if (request()->filled('search_national_id') && !empty(request('search_national_id'))) {
            $search = request('search_national_id');
            $query->whereHas('user', function ($q) use ($search) {
                $q->whereHas('student', function ($studentQ) use ($search) {
                    $studentQ->where('student_id', 'LIKE', '%' . $search . '%');
                })
                ->orWhereHas('staff', function ($staffQ) use ($search) {
                    $staffQ->where('staff_id', 'LIKE', '%' . $search . '%');
                });
            });
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
        
        if (request()->filled('search_active')) {
            $query->where('active', request('search_active'));
        }
        
        if (request()->filled('search_academic_term_id')) {
            $query->where('academic_term_id', request('search_academic_term_id'));
        }
        

        // Search by building
        if (request()->filled('search_building_id')) {
            $buildingId = request('search_building_id');
            $query->whereHas('accommodation.accommodatable.apartment.building', function ($q) use ($buildingId) {
                $q->where('id', $buildingId);
            });
        }

        // Search by apartment number
        if (request()->filled('search_apartment_number')) {
            $apartmentNumber = request('search_apartment_number');
            $query->whereHas('accommodation.accommodatable.apartment', function ($q) use ($apartmentNumber) {
                $q->where('number', $apartmentNumber);
            });
        }

        // Search by room number
        if (request()->filled('search_room_number')) {
            $roomNumber = request('search_room_number');
            $query->whereHas('accommodation.accommodatable', function ($q) use ($roomNumber) {
                $q->where('accommodatable_type', 'App\\Models\\Housing\\Room')
                  ->where('number', $roomNumber);
            });
        }
        
        return $query;
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param Reservation $reservation
     * @return string
     */
    protected function renderActionButtons($reservation): string
    {
        return view('components.ui.datatable.data-table-actions', [
            'mode' => 'single',
            'actions' => [],
            'id' => $reservation->id ?? '',
            'type' => 'Reservation',
            'singleActions' => [
                [
                    'action' => 'cancel',
                    'icon' => 'bx bx-block',
                    'class' => 'btn-danger',
                    'label' => 'Cancel'
                ]
            ]
        ])->render();
    }

    /**
     * Get accommodation information for display.
     *
     * @param Reservation $reservation
     * @return string
     */
    private function getAccommodationInfo(Reservation $reservation): string
    {
        if (!$reservation->accommodation) {
            return 'N/A';
        }
        return $reservation->accommodation->detail ?? 'N/A';
    }

} 