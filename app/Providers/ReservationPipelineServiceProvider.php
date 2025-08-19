<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Pipeline Operations
use App\Services\Reservation\Pipeline\Operations\CreateReservation;
use App\Services\Reservation\Pipeline\Operations\CheckInReservation;
use App\Services\Reservation\Pipeline\Operations\CheckOutReservation;
use App\Services\Reservation\Pipeline\Operations\CancelReservation;
use App\Services\Reservation\Pipeline\Operations\AcceptReservationRequest;

// Shared Services
use App\Services\Reservation\Pipeline\Services\Shared\ReservationValidator;
use App\Services\Reservation\Pipeline\Services\Shared\ReservationCreator;
use App\Services\Reservation\Pipeline\Services\Shared\AccommodationService;
use App\Services\Reservation\Pipeline\Services\Shared\PaymentService;

// CheckIn Services
use App\Services\Reservation\Pipeline\Services\CheckIn\ReservationValidator as CheckInReservationValidator;
use App\Services\Reservation\Pipeline\Services\CheckIn\EquipmentAssignmentService;
use App\Services\Reservation\Pipeline\Services\CheckIn\ReservationCheckIn;

// CheckOut Services
use App\Services\Reservation\Pipeline\Services\CheckOut\ReservationValidator as CheckOutReservationValidator;
use App\Services\Reservation\Pipeline\Services\CheckOut\EquipmentReturnService;
use App\Services\Reservation\Pipeline\Services\CheckOut\ReservationCheckOut;
use App\Services\Reservation\Pipeline\Services\CheckOut\PaymentService as CheckOutPaymentService;

// Cancel Services
use App\Services\Reservation\Pipeline\Services\Cancel\ReservationValidator as CancelReservationValidator;
use App\Services\Reservation\Pipeline\Services\Cancel\ReservationCancel;
use App\Services\Reservation\Pipeline\Services\Cancel\PaymentService as CancelPaymentService;

// Shared Pipes
use App\Services\Reservation\Pipeline\Pipes\Shared\ValidateReservationData;
use App\Services\Reservation\Pipeline\Pipes\Shared\CreateReservationRecord;
use App\Services\Reservation\Pipeline\Pipes\Shared\CreateAccommodation;
use App\Services\Reservation\Pipeline\Pipes\Shared\CreatePaymentRecord;

// CheckIn Specific Pipes
use App\Services\Reservation\Pipeline\Pipes\CheckIn\ValidateCheckIn;
use App\Services\Reservation\Pipeline\Pipes\CheckIn\AssignEquipment;
use App\Services\Reservation\Pipeline\Pipes\CheckIn\ProcessCheckIn;

// CheckOut Specific Pipes
use App\Services\Reservation\Pipeline\Pipes\CheckOut\ValidateCheckOut;
use App\Services\Reservation\Pipeline\Pipes\CheckOut\ReturnEquipment;
use App\Services\Reservation\Pipeline\Pipes\CheckOut\ProcessCheckOut;
use App\Services\Reservation\Pipeline\Pipes\CheckOut\CreateDamagePayment;

// Cancel Specific Pipes
use App\Services\Reservation\Pipeline\Pipes\Cancel\ValidateCancellation;
use App\Services\Reservation\Pipeline\Pipes\Cancel\CancelReservation as CancelReservationPipe;
use App\Services\Reservation\Pipeline\Pipes\Cancel\CancelPayment;

// Request Specific Pipes
use App\Services\Reservation\Pipeline\Pipes\Request\FindReservationRequest;
use App\Services\Reservation\Pipeline\Pipes\Request\ValidateRequestDuplicates;
use App\Services\Reservation\Pipeline\Pipes\Request\CreateReservationFromRequest;
use App\Services\Reservation\Pipeline\Pipes\Request\CreateRequestAccommodation;
use App\Services\Reservation\Pipeline\Pipes\Request\UpdateRequestStatus;

class ReservationPipelineServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Pipeline Operations
        $this->app->bind(CreateReservation::class);
        $this->app->bind(CheckInReservation::class);
        $this->app->bind(CheckOutReservation::class);
        $this->app->bind(CancelReservation::class);
        $this->app->bind(AcceptReservationRequest::class);

        // Register Shared Services
        $this->app->bind(ReservationValidator::class);
        $this->app->bind(ReservationCreator::class);
        $this->app->bind(AccommodationService::class);
        $this->app->bind(PaymentService::class);

        // Register CheckIn Services
        $this->app->bind(CheckInReservationValidator::class);
        $this->app->bind(EquipmentAssignmentService::class);
        $this->app->bind(ReservationCheckIn::class);

        // Register CheckOut Services
        $this->app->bind(CheckOutReservationValidator::class);
        $this->app->bind(EquipmentReturnService::class);
        $this->app->bind(ReservationCheckOut::class);
        $this->app->bind(CheckOutPaymentService::class);

        // Register Cancel Services
        $this->app->bind(CancelReservationValidator::class);
        $this->app->bind(ReservationCancel::class);
        $this->app->bind(CancelPaymentService::class);

        // Register Shared Pipes
        $this->app->bind(ValidateReservationData::class);
        $this->app->bind(CreateReservationRecord::class);
        $this->app->bind(CreateAccommodation::class);
        $this->app->bind(CreatePaymentRecord::class);

        // Register CheckIn Specific Pipes
        $this->app->bind(ValidateCheckIn::class);
        $this->app->bind(AssignEquipment::class);
        $this->app->bind(ProcessCheckIn::class);

        // Register CheckOut Specific Pipes
        $this->app->bind(ValidateCheckOut::class);
        $this->app->bind(ReturnEquipment::class);
        $this->app->bind(ProcessCheckOut::class);
        $this->app->bind(CreateDamagePayment::class);

        // Register Cancel Specific Pipes
        $this->app->bind(ValidateCancellation::class);
        $this->app->bind(CancelReservationPipe::class);
        $this->app->bind(CancelPayment::class);

        // Register Request Specific Pipes
        $this->app->bind(FindReservationRequest::class);
        $this->app->bind(ValidateRequestDuplicates::class);
        $this->app->bind(CreateReservationFromRequest::class);
        $this->app->bind(CreateRequestAccommodation::class);
        $this->app->bind(UpdateRequestStatus::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
