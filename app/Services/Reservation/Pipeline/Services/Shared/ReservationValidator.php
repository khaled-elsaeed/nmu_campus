<?php

namespace App\Services\Reservation\Pipeline\Services\Shared;

use App\Models\Reservation\Reservation;
use App\Exceptions\BusinessValidationException;
use Illuminate\Support\Facades\DB;

class ReservationValidator
{
    /**
     * Check for duplicate reservations.
     *
     * @param int $userId
     * @param string $periodType
     * @param int|null $academicTermId
     * @param string|null $checkInDate
     * @param string|null $checkOutDate
     * @throws BusinessValidationException
     */
    public function checkForDuplicateReservation(
        int $userId,
        string $periodType,
        ?int $academicTermId = null,
        ?string $checkInDate = null,
        ?string $checkOutDate = null
    ): void {
        $query = Reservation::where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed');

        if ($periodType === 'academic') {
            $query->where('academic_term_id', $academicTermId);
        } elseif ($periodType === 'calendar') {
            $query->where(function ($q) use ($checkInDate, $checkOutDate) {
                $q->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                  ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                  ->orWhere(function ($subQ) use ($checkInDate, $checkOutDate) {
                      $subQ->where('check_in_date', '<=', $checkInDate)
                           ->where('check_out_date', '>=', $checkOutDate);
                  });
            });
        }

        $existingReservation = $query->first();

        if ($existingReservation) {
            throw new BusinessValidationException(__('User already has an active reservation for this period.'));
        }
    }

    /**
     * Validate reservation data.
     *
     * @param array $data
     * @throws BusinessValidationException
     */
    public function validateReservationData(array $data): void
    {
        $requiredFields = ['user_id', 'period_type'];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new BusinessValidationException(
                    __("validation.required", ['attribute' => str_replace('_', ' ', $field)])
                );
            }
        }

        if ($data['period_type'] === 'academic' && empty($data['academic_term_id'])) {
            throw new BusinessValidationException(__('Academic term is required for academic period reservations.'));
        }

        if ($data['period_type'] === 'calendar' && (empty($data['check_in_date']) || empty($data['check_out_date']))) {
            throw new BusinessValidationException(__('Check-in and check-out dates are required for calendar period reservations.'));
        }
    }
}
