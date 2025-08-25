<?php

namespace App\Services\Home;

use App\Models\Reservation\Reservation;
use App\Models\Reservation\ReservationRequest;

use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * Service layer for student home page data aggregation.
 */
class StudentHomeService
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * Get basic student details.
     *
     * @return array
     * @throws Exception When there is no authenticated user
     */
    public function getUserDetails(): array
    {
        if (!$this->user) {
            throw new Exception('No authenticated user found.');
        }

        return [
            'id'         => $this->user->id,
            'username'   => $this->user->name,
            'last_login' => $this->user->last_login,
        ];
    }

    /**
     * Get active reservations for the student.
     *
     * @return array
     * @throws Exception When there is no authenticated user
     */
    public function getActiveReservation(): array
    {
        if (!$this->user) {
            throw new Exception('No authenticated user found.');
        }

        $activeReservations = $this->user->reservations->where('status', 'active');

        if (!$activeReservations || $activeReservations->isEmpty()) {
            return [];
        }

        return $activeReservations->map(function ($reservation) {
            return [
                'reservation_number' => $reservation->reservation_number,
                'status'             => $reservation->status,
                'check_in_date'      => $reservation->check_in_date,
                'check_out_date'     => $reservation->check_out_date,
                'period_type'        => $reservation->period_type,
                'is_long_term'       => $reservation->isLongTerm(),
            ];
        })->toArray();
    }

    /**
     * Get neighbors for active reservation.
     *
     * @return array
     * @throws Exception When there is no authenticated user
     */
   public function getActiveReservationNeighbors(): array
    {
        if (!$this->user) {
            throw new Exception('No authenticated user found.');
        }

        $activeReservation = $this->user->reservations->where('status','active')->first();

        if (!$activeReservation) {
            return [];
        }

        // Only handle room reservations for now
        if ($activeReservation->type !== 'room') {
            return [];
        }

        $apartment = $activeReservation->accommodation->room->apartment;

        $reservationNeighbors = Reservation::whereHas('accommodation.room', function ($query) use ($apartment) {
                $query->where('apartment_id', $apartment->id);
            })
            ->where('id', '!=', $activeReservation->id)
            ->where('status', '!=', 'cancelled')
            ->get();

        $neighbors = [];

        foreach ($reservationNeighbors as $neighbor) {
            $location = null;
            if (
                $neighbor->accommodation &&
                $neighbor->accommodation->room &&
                $neighbor->accommodation->room->apartment &&
                $neighbor->accommodation->room->apartment->building
            ) {
                $buildingNumber = $neighbor->accommodation->room->apartment->building->number;
                $apartmentNumber = $neighbor->accommodation->room->apartment->number;
                $roomNumber = $neighbor->accommodation->room->number;
                $location = 'B' . $buildingNumber . 'A' . $apartmentNumber . 'R' . $roomNumber;
            }

            $neighbors[] = [
                'name'       => $neighbor->user->name,
                'location'   => $location,
                'program'    => $neighbor->user->student->program->name,
                'faculty'    => $neighbor->user->student->program->faculty->name,
                'year'       => $neighbor->user->student->level,
                'phone'      => $neighbor->user->student->phone,
            ];
        }

        return $neighbors;
    }

    /**
     * Get reservation requests for the student.
     *
     * @return array
     * @throws Exception When there is no authenticated user
     */
    public function getReservationRequests(): array
    {
        if (!$this->user) {
            throw new Exception('No authenticated user found.');
        }

        $requests = $this->user->reservationRequests()->get();

        return $requests->map(function ($request) {
            return [
                'id' => $request->id,
                'request_number' => $request->request_number,
                'user_id' => $request->user_id,
                'period_type' => $request->period_type,
                'academic_term_id' => $request->academic_term_id,
                'accommodation_type' => $request->accommodation_type,
                'room_type' => $request->room_type,
                'bed_count' => $request->bed_count,
                'status' => $request->status,
                'reviewed_by' => $request->reviewed_by,
                'reviewed_at' => formatDate($request->reviewed_at),
                'approved_at' => formatDate($request->approved_at),
                'rejected_at' => formatDate($request->rejected_at),
                'rejection_reason' => $request->rejection_reason,
                'stay_with_sibling' => $request->stay_with_sibling,
                'sibling_name' => $request?->sibling?->name,
                'created_at' => formatDate($request->created_at),
            ];
        })->toArray();
    }


    /**
     * Get upcoming student events.
     * (Static mockup for now, should come from DB or API later)
     *
     * @return array
     */
    public function getUpcomingEvents(): array
    {
        return [
        
        ];
    }

    /**
     * Data needed to open the New Request modal.
     *
     * @return array
     * @throws Exception When there is no authenticated user
     */
    public function getNewRequestData(): array
    {
        if (!$this->user) {
            throw new Exception('No authenticated user found.');
        }

        $userGender = $this->user->gender;

        $siblings = $this->user->siblings()->when($userGender, function($q) use ($userGender) {
            $q->where('gender', $userGender);
        })->get();

        $lastReservation = Reservation::where('user_id', $this->user->id)
            ->whereIn('period_type', ['academic'])
            ->where('status', '!=', 'active')
            ->latest('id')
            ->first();

        $location = null;
        if ($lastReservation) {
            $location = [];
            if ($lastReservation->accommodation) {
                $location[] = 'B' . $lastReservation->accommodation->room->apartment->building->number;
                $location[] = 'A' . $lastReservation->accommodation->room->apartment->number;
                $location[] = 'R' . $lastReservation->accommodation->room->number;
            }
        }

        return [
            'siblings_same_gender' => $siblings->map(function($s){
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'gender' => $s->gender ?? null,
                ];
            })->toArray(),
            'last_term' => [
                'has_last' => (bool) $lastReservation ? false : true,
                'location' => $location ? $location : ['B1', 'A2', 'R3'], // mock data if empty
            ],
        ];
    }

    /**
     * Create a new reservation request from student payload.
     *
     * @param array $payload
     * @return array
     * @throws Exception When there is no authenticated user or required data missing
     */
    public function createReservationRequest(array $payload): array
    {
        if (!$this->user) {
            throw new Exception('No authenticated user found.');
        }

        // Basic validation (controller/request class can be added later)
        $periodType = $payload['period_type'] ?? 'academic';
        if ($periodType === 'academic' && empty($payload['academic_term_id'])) {
            throw new Exception('Academic term is required for academic period.');
        }

        $stayWithSibling = !empty($payload['stay_with_sibling']) ? 1 : 0;
        $roomType = $payload['room_type'] ?? 'single';
        $bedCount = (int)($payload['bed_count'] ?? 1);
        if ($stayWithSibling) {
            $roomType = 'double';
            $bedCount = 1;
        }

        $request = new ReservationRequest();
        $request->user_id = $this->user->id;
        $request->period_type = $periodType;
        $request->academic_term_id = $payload['academic_term_id'] ?? null;
        $request->accommodation_type = 'room';
        $request->room_type = $roomType;
        $request->bed_count = $bedCount;
        $request->stay_with_sibling = $stayWithSibling;
        $request->sibling_id = $stayWithSibling ? ($payload['sibling_id'] ?? null) : null;
        $request->status = 'pending';
        $request->save();

        return [
            'id' => $request->id,
            'request_number' => $request->request_number,
        ];
    }
}
