<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Notifications\EmailVerification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;
use Illuminate\Support\Facades\Log;

class SendVerificationNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $event->user->notify(new EmailVerification());
    }

    /**
     * Handle a job failure.
     *
     * @param UserRegistered $event
     * @param \Throwable $exception
     * @return void
     */
    public function failed(UserRegistered $event, Throwable $exception): void
    {
        Log::error('Failed to send verification email to user: ' . ($event->user->email ?? 'unknown'), [
            'exception' => $exception->getMessage(),
            'user_id' => $event->user->id ?? null,
        ]);
    }
}
