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
use App\Services\Reservation\Operation\CreateReservationService;
use App\Services\Reservation\Operation\CancelReservationService;
use App\Services\Reservation\Operation\CheckOutReservationService;
use App\Services\Reservation\Operation\CheckInReservationService;
use Carbon\Carbon;

class ReservationService
{
    public function __construct(
        protected CreateReservationService $createReservationService,
        protected CheckInReservationService $checkInService,
        protected CheckOutReservationService $checkOutService,
        protected CancelReservationService $cancelReservationService
    ) {}

    /**
     * Find a reservation by its reservation number.
     *
     * @param string $number
     * @return Reservation|null
     */
 
    public function findByNumber(string $number): ?Reservation
    {
        $reservation = Reservation::with(['user', 'accommodation', 'academicTerm','equipmentTracking.equipmentDetails.equipment'])
            ->where('reservation_number', $number)
            ->first();

        if (!$reservation) {
            return null;
        }

        $reservation->location = $this->getLocation($reservation);
        $reservation->period = $this->getPeriod($reservation);
        $reservation->status = __(ucfirst(str_replace("_", " ", $reservation->status)));

        return $reservation;
    }
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

    public function checkIn(array $data){
        return DB::transaction(function () use ($data) {
            return $this->checkInService->checkIn($data);
        });
    }

    /**
     * Check out a reservation.
     *
     * @param array $data
     */
    public function checkOut(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->checkOutService->checkOut($data);
        });
    }

    /**
     * Cancel a reservation.
     *
     * @param int $reservationId
     * @param CancelReservationService $cancelReservationService
     * @throws BusinessValidationException
     */
    public function cancelReservation(int $reservationId): void
    {
        DB::transaction(function () use ($reservationId) {
            $this->cancelReservationService->cancel($reservationId);
        });
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
            throw new BusinessValidationException(__('Cannot delete a reservation that has been checked in.'));
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
        $stats = Reservation::leftJoin('users', 'reservations.user_id', '=', 'users.id')
                ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN users.gender = 'male' THEN 1 ELSE 0 END) as total_male,
                SUM(CASE WHEN users.gender = 'female' THEN 1 ELSE 0 END) as total_female,
                SUM(CASE WHEN reservations.status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN reservations.status = 'pending' AND users.gender = 'male' THEN 1 ELSE 0 END) as pending_male,
                SUM(CASE WHEN reservations.status = 'pending' AND users.gender = 'female' THEN 1 ELSE 0 END) as pending_female,
                SUM(CASE WHEN reservations.status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN reservations.status = 'confirmed' AND users.gender = 'male' THEN 1 ELSE 0 END) as confirmed_male,
                SUM(CASE WHEN reservations.status = 'confirmed' AND users.gender = 'female' THEN 1 ELSE 0 END) as confirmed_female,
                SUM(CASE WHEN reservations.status = 'checked_in' THEN 1 ELSE 0 END) as checked_in,
                SUM(CASE WHEN reservations.status = 'checked_in' AND users.gender = 'male' THEN 1 ELSE 0 END) as checked_in_male,
                SUM(CASE WHEN reservations.status = 'checked_in' AND users.gender = 'female' THEN 1 ELSE 0 END) as checked_in_female,
                MAX(reservations.updated_at) as last_update_time,
                MAX(CASE WHEN reservations.status = 'pending' THEN reservations.updated_at ELSE NULL END) as pending_last_update,
                MAX(CASE WHEN reservations.status = 'confirmed' THEN reservations.updated_at ELSE NULL END) as confirmed_last_update,
                MAX(CASE WHEN reservations.status = 'checked_in' THEN reservations.updated_at ELSE NULL END) as checked_in_last_update
            ")->first();

        $lastUpdateTime = formatDate($stats->last_update_time);
        $pendingLastUpdate = formatDate($stats->pending_last_update);
        $confirmedLastUpdate = formatDate($stats->confirmed_last_update);
        $checkedInLastUpdate = formatDate($stats->checked_in_last_update);

        return [
            'reservations' => [
                'count' => formatNumber($stats->total),
                'male' => formatNumber($stats->total_male),
                'female' => formatNumber($stats->total_female),
                'lastUpdateTime' => $lastUpdateTime
            ],
            'pending' => [
                'count' => formatNumber($stats->pending),
                'male' => formatNumber($stats->pending_male),
                'female' => formatNumber($stats->pending_female),
                'lastUpdateTime' => $pendingLastUpdate
            ],
            'confirmed' => [
                'count' => formatNumber($stats->confirmed),
                'male' => formatNumber($stats->confirmed_male),
                'female' => formatNumber($stats->confirmed_female),
                'lastUpdateTime' => $confirmedLastUpdate
            ],
            'checked_in' => [
                'count' => formatNumber($stats->checked_in),
                'male' => formatNumber($stats->checked_in_male),
                'female' => formatNumber($stats->checked_in_female),
                'lastUpdateTime' => $checkedInLastUpdate
            ],
            
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
            ->addColumn('name', fn($reservation) => $reservation->user?->name ?? 'N/A')
            ->addColumn('location', fn($reservation) => $this->getLocation($reservation))
            ->addColumn('period', fn($reservation) => $this->getPeriod($reservation))
            ->addColumn('status', fn($reservation) => ucfirst($reservation->status))
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
        
        
        if (request()->filled('search_academic_term')) {
            $query->where('academic_term_id', request('search_academic_term'));
        }
        

        // Search by building
        if (request()->filled('search_building')) {
            $buildingId = request('search_building');
            $query->where(function ($q) use ($buildingId) {
                $q->whereHas('accommodation.room.apartment.building', function ($subQ) use ($buildingId) {
                    $subQ->where('id', $buildingId);
                })->orWhereHas('accommodation.apartment.building', function ($subQ) use ($buildingId) {
                    $subQ->where('id', $buildingId);
                });
            });
        }

        // Search by apartment number
        if (request()->filled('search_apartment')) {
            $apartmentNumber = request('search_apartment');
            $query->where(function ($q) use ($apartmentNumber) {
                $q->whereHas('accommodation.room.apartment', function ($subQ) use ($apartmentNumber) {
                    $subQ->where('number', $apartmentNumber);
                })->orWhereHas('accommodation.apartment', function ($subQ) use ($apartmentNumber) {
                    $subQ->where('number', $apartmentNumber);
                });
            });
        }

        // Search by room number
        if (request()->filled('search_room')) {
            $roomNumber = request('search_room');
            $query->whereHas('accommodation.room', function ($q) use ($roomNumber) {
                $q->where('number', $roomNumber);
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
        return view('components.ui.datatable.table-actions', [
            'mode' => 'single',
            'actions' => [],
            'id' => $reservation->id ?? '',
            'type' => 'Reservation',
            'singleActions' => [
                [
                    'action' => 'cancel',
                    'icon' => 'bx bx-block',
                    'class' => 'btn-danger',
                    'label' => __('Cancel')
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
    private function getLocation(Reservation $reservation): string
    {
        $location = 'N/A';

        if (!$reservation->accommodation) {
            return 'N/A';
        }

        switch($reservation->accommodation->type) {
            case 'room':
                $room = $reservation->accommodation->room;
                $location = 'B' . $room->apartment->building->number . ' - A' . $room->apartment->number . ' - R' . $room->number;

                break;
            case 'apartment':
                $apartment = $reservation->accommodation->apartment;
                $location = 'B' . $apartment->building->number . ' - A' . $apartment->number;
                break;
            default:
                return 'N/A';
        }

        return $location;
    }

    /**
     * Get period information for display.
     *
     * @param Reservation $reservation
     * @return string
     */
    private function getPeriod(Reservation $reservation): string
    {
        $period = 'N/A';

        switch($reservation->period_type) {
            case 'academic':
                $period = $reservation->academicTerm->name ?? 'N/A';
                break;
            case 'calendar':
                if ($reservation->check_in_date && $reservation->check_out_date) {
                    $checkInDate = formatDate($reservation->check_in_date);
                    $checkOutDate = formatDate($reservation->check_out_date);
                    $period = $checkInDate . ' - ' . $checkOutDate;
                }
                break;
        }

        return $period;
    }
}