<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Reservation;

class ActivateReservation implements ShouldQueue
{
    use Queueable;

    public $queue = 'reservation_activation';
    public $tries = 3; // Add retry attempts
    public $backoff = [10, 30, 60]; // Backoff delays in seconds

    public function __construct(
        public int $reservationId
    ) {}

    public function handle()
    {
        DB::transaction(function () {
            // More efficient query - just lock and update
            $updated = Reservation::where('id', $this->reservationId)
                ->where('status', 'confirmed')
                ->where('active', false)
                ->lockForUpdate()
                ->update([
                    'status' => 'active',
                    'active' => true,
                    'activated_at' => now(), // Consider adding timestamp
                ]);

            if ($updated === 0) {
                Log::warning("Reservation {$this->reservationId} was not activated - may have been processed already or doesn't meet criteria");
            }
        });
    }

    public function failed(\Throwable $exception)
    {
        Log::error("Failed to activate reservation {$this->reservationId} after {$this->tries} attempts: " . $exception->getMessage(), [
            'reservation_id' => $this->reservationId,
            'exception' => $exception,
        ]);
    }
}