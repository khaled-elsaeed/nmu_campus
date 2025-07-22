<?php

namespace App\Services\Reservation\Create;

use App\Models\Reservation\Reservation;
use App\Models\Academic\AcademicTerm;
use App\Exceptions\BusinessValidationException;
use Carbon\Carbon;

class ReservationValidator
{
    /**
     * Check for duplicate reservations based on academic term or date overlap
     *
     * @param array $data
     * @param int|null $excludeReservationId - ID to exclude (for updates)
     * @throws BusinessValidationException
     */
    public function checkForDuplicateReservation(array $data, ?int $excludeReservationId = null): void
    {
        $this->validateInputData($data);

        $userId = $data['user_id'];
        $academicTermId = $data['academic_term_id'] ?? null;
        $checkInDate = $data['check_in_date'] ?? null;
        $checkOutDate = $data['check_out_date'] ?? null;

        $conflictingReservation = $this->findConflictingReservation(
            $userId,
            $academicTermId,
            $checkInDate,
            $checkOutDate,
            $excludeReservationId
        );

        if ($conflictingReservation) {
            $this->throwConflictException($conflictingReservation, $academicTermId);
        }
    }

    /**
     * Validate input data based on period_type (academic or calendar)
     */
    private function validateInputData(array $data): void
    {
        if (empty($data['user_id'])) {
            throw new BusinessValidationException('User ID is required.');
        }

        $periodType = $data['period_type'] ?? null;

        if ($periodType === 'academic') {
            if (empty($data['academic_term_id'])) {
                throw new BusinessValidationException('Academic term ID is required for academic period.');
            }
            // Also check that the academic term is active
            $term = AcademicTerm::find($data['academic_term_id']);
            if (!$term || !$term->active) {
                throw new BusinessValidationException('Selected academic term is not active.');
            }
        } elseif ($periodType === 'calendar') {
            $checkInDate = $data['check_in_date'] ?? null;
            $checkOutDate = $data['check_out_date'] ?? null;

            if (!$checkInDate || !$checkOutDate) {
                throw new BusinessValidationException('Check-in and check-out dates are required for calendar period.');
            }

            $checkIn = Carbon::parse($checkInDate);
            $checkOut = Carbon::parse($checkOutDate);

            if ($checkOut->lte($checkIn)) {
                throw new BusinessValidationException('Check-out date must be after check-in date.');
            }
        } else {
            throw new BusinessValidationException('Period type must be either academic or calendar.');
        }
    }

    /**
     * Find conflicting reservation
     */
    private function findConflictingReservation(
        int $userId,
        ?int $academicTermId,
        ?string $checkInDate,
        ?string $checkOutDate,
        ?int $excludeReservationId
    ): ?Reservation {
        $query = Reservation::where('user_id', $userId)
            ->where('status', '!=', 'cancelled');

        // Exclude current reservation if updating
        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        // Build the conflict conditions
        $query->where(function ($q) use ($academicTermId, $checkInDate, $checkOutDate, $userId) {
            // Check for academic term conflicts
            if ($academicTermId) {
                $q->where('academic_term_id', $academicTermId);
            }

            // Check for date overlaps (always check if dates provided)
            if ($checkInDate && $checkOutDate) {
                $dateConflictQuery = function ($subQ) use ($checkInDate, $checkOutDate) {
                    $subQ->where(function ($dateQ) use ($checkInDate, $checkOutDate) {
                        // Standard overlap detection: new start < existing end AND new end > existing start
                        $dateQ->where('check_in_date', '<', $checkOutDate)
                              ->where('check_out_date', '>', $checkInDate);
                    });
                };

                if ($academicTermId) {
                    // If academic term provided, check date overlaps with OR condition
                    $q->orWhere($dateConflictQuery);
                } else {
                    // If no academic term, only check date overlaps
                    $q->where($dateConflictQuery);
                }
            }
        });

        return $query->first();
    }

    /**
     * Throw appropriate conflict exception with detailed message
     */
    private function throwConflictException(Reservation $conflictingReservation, ?int $academicTermId): void
    {
        if ($academicTermId && $conflictingReservation->academic_term_id === $academicTermId) {
            throw new BusinessValidationException(
                'A reservation already exists for this academic term.'
            );
        }

        if ($conflictingReservation->check_in_date && $conflictingReservation->check_out_date) {
            $formatDate = function ($date) {
                return Carbon::parse($date)->format('M j, Y');
            };

            throw new BusinessValidationException(
                sprintf(
                    'A reservation already exists with overlapping dates (%s to %s).',
                    $formatDate($conflictingReservation->check_in_date),
                    $formatDate($conflictingReservation->check_out_date)
                )
            );
        }

        throw new BusinessValidationException(
            'A conflicting reservation already exists for this user.'
        );
    }

    /**
     * Check if user has any active reservations (utility method)
     */
    public function hasActiveReservations(int $userId): bool
    {
        return Reservation::where('user_id', $userId)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->exists();
    }

    /**
     * Get user's conflicting reservations for detailed error reporting
     */
    public function getUserConflictingReservations(int $userId, string $checkInDate, string $checkOutDate): array
    {
        return Reservation::where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->where('check_in_date', '<', $checkOutDate)
            ->where('check_out_date', '>', $checkInDate)
            ->get()
            ->toArray();
    }
}