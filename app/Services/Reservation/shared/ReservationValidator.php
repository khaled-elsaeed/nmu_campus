<?php

namespace App\Services\Reservation\Shared;

use App\Models\Reservation\Reservation;
use App\Models\Academic\AcademicTerm;
use App\Exceptions\BusinessValidationException;
use Carbon\Carbon;

class ReservationValidator
{
    /**
     * Check for duplicate reservations based on academic term or date overlap
     *
     * @param int $userId
     * @param string $periodType
     * @param int|null $academicTermId
     * @param string|null $checkInDate
     * @param string|null $checkOutDate
     * @param int|null $excludeReservationId - ID to exclude (for updates)
     * @throws BusinessValidationException
     */
    public function checkForDuplicateReservation(
        int $userId,
        string $periodType,
        ?int $academicTermId = null,
        ?string $checkInDate = null,
        ?string $checkOutDate = null,
        ?int $excludeReservationId = null
    ): void {

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
     * Find conflicting reservation
     */
    private function findConflictingReservation(
        int $userId,
        ?int $academicTermId,
        ?string $checkInDate,
        ?string $checkOutDate,
        ?int $excludeReservationId
    ): ?Reservation {
        $conflictingReservation = Reservation::findConflictingReservation(
            $userId,
            $academicTermId,
            $checkInDate,
            $checkOutDate
        );

        // Exclude current reservation if updating
        if ($conflictingReservation && $excludeReservationId && $conflictingReservation->id === $excludeReservationId) {
            return null;
        }

        return $conflictingReservation;
    }

    /**
     * Throw appropriate conflict exception with detailed message
     */
    private function throwConflictException(Reservation $conflictingReservation, ?int $academicTermId): void
    {
        if ($academicTermId && $conflictingReservation->academic_term_id === $academicTermId) {
            throw new BusinessValidationException(
                __('A reservation already exists for this academic term.')
            );
        }

        if ($conflictingReservation->check_in_date && $conflictingReservation->check_out_date) {
            $formatDate = function ($date) {
                return Carbon::parse($date)->format('M j, Y');
            };

            throw new BusinessValidationException(__('A reservation already exists with overlapping dates of another reservation'));
        }

        throw new BusinessValidationException(__('A conflicting reservation already exists for this user.'));
    }

    /**
     * Check if user has any active reservations (utility method)
     */
    public function hasActiveReservations(int $userId): bool
    {
        return Reservation::hasActiveReservations($userId);
    }

    /**
     * Get user's conflicting reservations for detailed error reporting
     */
    public function getUserConflictingReservations(int $userId, string $checkInDate, string $checkOutDate): array
    {
        return Reservation::getConflictingReservations($userId, $checkInDate, $checkOutDate)->toArray();
    }
}