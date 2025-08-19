<?php

namespace App\Services\Reservation\Pipeline\Pipes;

use Closure;
use App\Models\Reservation\ReservationRequest;
use App\Exceptions\BusinessValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FindReservationRequest
{
    /**
     * Handle the incoming request.
     *
     * @param array $data
     * @param Closure $next
     * @return mixed
     * @throws ModelNotFoundException
     * @throws BusinessValidationException
     */
    public function handle(array $data, Closure $next)
    {
        $reservationRequest = ReservationRequest::with(['user', 'academicTerm'])
            ->find($data['reservation_request_id']);

        if (!$reservationRequest) {
            throw new ModelNotFoundException(__('Reservation request not found.'));
        }

        if ($reservationRequest->status !== 'pending') {
            throw new BusinessValidationException(__('Reservation request is not in pending status.'));
        }

        $data['reservation_request'] = $reservationRequest;
        return $next($data);
    }
}
