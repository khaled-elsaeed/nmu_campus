# Reservation Pipeline System

This directory contains the refactored reservation system using Laravel's Pipeline pattern. The system has been reorganized to be more modular, testable, and maintainable.

## Directory Structure

```
app/Services/Reservation/Pipeline/
├── README.md                           # This file
├── ReservationPipeline.php             # Base pipeline class
├── Operations/                         # Main pipeline operations
│   ├── CreateReservation.php          # Create reservation pipeline
│   ├── CheckInReservation.php         # Check-in pipeline
│   ├── CheckOutReservation.php        # Check-out pipeline
│   ├── CancelReservation.php          # Cancel reservation pipeline
│   └── AcceptReservationRequest.php   # Accept request pipeline
└── Pipes/                             # Pipeline pipes organized by operation
    ├── Shared/                         # Shared pipes used across operations
    │   ├── ValidateReservationData.php
    │   ├── CreateReservationRecord.php
    │   ├── CreateAccommodation.php
    │   └── CreatePaymentRecord.php
    ├── CheckIn/                        # Check-in specific pipes
    │   ├── ValidateCheckIn.php
    │   ├── AssignEquipment.php
    │   └── ProcessCheckIn.php
    ├── CheckOut/                       # Check-out specific pipes
    │   ├── ValidateCheckOut.php
    │   ├── ReturnEquipment.php
    │   ├── ProcessCheckOut.php
    │   └── CreateDamagePayment.php
    ├── Cancel/                         # Cancel specific pipes
    │   ├── ValidateCancellation.php
    │   ├── CancelReservation.php
    │   └── CancelPayment.php
    └── Request/                        # Request specific pipes
        ├── FindReservationRequest.php
        ├── ValidateRequestDuplicates.php
        ├── CreateReservationFromRequest.php
        ├── CreateRequestAccommodation.php
        └── UpdateRequestStatus.php
```

## How It Works

### Base Pipeline Class

The `ReservationPipeline` abstract class provides the foundation for all reservation operations:

- `executePipeline()` - Executes pipes within a database transaction
- `executePipelineWithFinally()` - Executes pipes with cleanup using Laravel 12's `finally` method
- `logPipelineExecution()` - Logs pipeline execution for debugging

### Pipeline Operations

Each operation (Create, CheckIn, CheckOut, Cancel, AcceptRequest) extends the base pipeline and defines its specific pipe sequence.

### Pipeline Pipes

Each pipe implements the `handle()` method that:
1. Receives the data array
2. Processes the data
3. Calls `$next($data)` to pass data to the next pipe
4. Can modify the data array to pass information between pipes

## Usage Examples

### Creating a Reservation

```php
use App\Services\Reservation\Pipeline\Operations\CreateReservation;

$createPipeline = app(CreateReservation::class);
$reservation = $createPipeline->execute([
    'user_id' => 1,
    'period_type' => 'academic',
    'academic_term_id' => 1,
    'building_id' => 1,
    'apartment_id' => 1,
    'room_id' => 1,
    'bed_count' => 1
]);
```

### Checking In a Reservation

```php
use App\Services\Reservation\Pipeline\Operations\CheckInReservation;

$checkInPipeline = app(CheckInReservation::class);
$checkInPipeline->execute([
    'reservation_id' => 1,
    'equipment' => [
        ['equipment_id' => 1, 'quantity' => 1],
        ['equipment_id' => 2, 'quantity' => 2]
    ]
]);
```

### Checking Out a Reservation

```php
use App\Services\Reservation\Pipeline\Operations\CheckOutReservation;

$checkOutPipeline = app(CheckOutReservation::class);
$reservation = $checkOutPipeline->execute([
    'reservation_id' => 1,
    'equipment' => [
        [
            'equipment_id' => 1,
            'quantity' => 1,
            'returned_status' => 'good'
        ],
        [
            'equipment_id' => 2,
            'quantity' => 1,
            'returned_status' => 'damaged',
            'estimated_cost' => 50.00,
            'notes' => 'Broken handle'
        ]
    ]
]);
```

### Canceling a Reservation

```php
use App\Services\Reservation\Pipeline\Operations\CancelReservation;

$cancelPipeline = app(CancelReservation::class);
$cancelPipeline->execute(1); // reservation_id
```

### Accepting a Reservation Request

```php
use App\Services\Reservation\Pipeline\Operations\AcceptReservationRequest;

$acceptPipeline = app(AcceptReservationRequest::class);
$reservation = $acceptPipeline->execute(
    reservationRequestId: 1,
    accommodationType: 'room',
    roomId: 1,
    apartmentId: null,
    bedCount: 1,
    notes: 'Approved for single room'
);
```

## Benefits of the Pipeline Pattern

1. **Modularity**: Each pipe has a single responsibility
2. **Testability**: Individual pipes can be unit tested
3. **Reusability**: Pipes can be reused across different operations
4. **Maintainability**: Easy to add, remove, or reorder steps
5. **Transaction Safety**: All operations are wrapped in database transactions
6. **Cleanup**: The `finally` method ensures proper cleanup regardless of success/failure
7. **Logging**: Built-in logging for debugging and monitoring

## Adding New Operations

To add a new operation:

1. Create a new operation class in `Operations/` directory
2. Extend `ReservationPipeline`
3. Define the pipe sequence in the `execute()` method
4. Create specific pipes in the appropriate `Pipes/` subdirectory
5. Register the new operation in `ReservationPipelineServiceProvider`

## Adding New Pipes

To add a new pipe:

1. Create a new pipe class in the appropriate `Pipes/` subdirectory
2. Implement the `handle()` method
3. Register the pipe in `ReservationPipelineServiceProvider`
4. Add the pipe to the appropriate operation's pipe sequence

## Service Provider Registration

The `ReservationPipelineServiceProvider` automatically registers all pipeline operations and pipes for dependency injection. Make sure to add this provider to your `config/app.php` providers array.

## Migration from Old Services

The old service classes have been replaced with pipeline operations. Update your controllers to use the new pipeline operations instead of the old services.

## Error Handling

All pipes can throw `BusinessValidationException` which will be caught by the pipeline and logged. The `finally` method ensures cleanup even when errors occur.
