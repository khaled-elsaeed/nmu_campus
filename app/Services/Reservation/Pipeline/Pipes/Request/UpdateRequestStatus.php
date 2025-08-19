<?php

namespace App\Services\Reservation\Pipeline\Pipes\Request;

use Closure;
use Illuminate\Support\Facades\Auth;

class UpdateRequestStatus
{
    /**
     * Handle the incoming request.
     *
     * @param array $data
     * @param Closure $next
     * @return mixed
     */
    public function handle(array $data, Closure $next)
    {
        $request = $data['reservation_request'];
        $reservation = $data['reservation'];

        $request->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'approved_at' => now(),
            'reservation_id' => $reservation->id
        ]);

        return $next($data);
    }
}
