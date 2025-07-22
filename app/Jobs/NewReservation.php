<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation\Reservation;
use App\Services\Reservation\CreateReservationService;
use App\Exceptions\BusinessValidationException;

class NewReservation implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    public array $data;
    public ?int $createdReservationId = null;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        DB::transaction(function () {
            $service = app(CreateReservationService::class);
            $reservation = $service->create($this->data);
            $this->createdReservationId = $reservation->id;
        });
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Failed to create reservation', [
            'data' => $this->data,
            'exception' => $exception,
        ]);
    }
} 