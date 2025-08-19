<?php

namespace App\Services\Reservation\Pipeline\Pipes;

use Closure;
use App\Services\Reservation\Shared\ReservationValidator;
use App\Exceptions\BusinessValidationException;

class ValidateReservationData
{
    public function __construct(
        protected ReservationValidator $validator
    ) {}

    /**
     * Handle the incoming request.
     *
     * @param array $data
     * @param Closure $next
     * @return mixed
     * @throws BusinessValidationException
     */
    public function handle(array $data, Closure $next)
    {
        // Validate for duplicate reservations
        $this->validator->checkForDuplicateReservation(
            $data['user_id'],
            $data['period_type'],
            $data['academic_term_id'] ?? null,
            $data['check_in_date'] ?? null,
            $data['check_out_date'] ?? null
        );

        // Validate required fields
        $this->validateRequiredFields($data);

        return $next($data);
    }

    /**
     * Validate required fields for reservation creation
     *
     * @param array $data
     * @throws BusinessValidationException
     */
    private function validateRequiredFields(array $data): void
    {
        $requiredFields = ['user_id', 'period_type'];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new BusinessValidationException(
                    __("validation.required", ['attribute' => str_replace('_', ' ', $field)])
                );
            }
        }

        // Validate period type specific requirements
        if ($data['period_type'] === 'academic' && empty($data['academic_term_id'])) {
            throw new BusinessValidationException(__('Academic term is required for academic period reservations.'));
        }

        if ($data['period_type'] === 'calendar' && (empty($data['check_in_date']) || empty($data['check_out_date']))) {
            throw new BusinessValidationException(__('Check-in and check-out dates are required for calendar period reservations.'));
        }
    }
}
