<?php

namespace App\Http\Controllers\Reservation;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\Reservation\ReservationService;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\StoreReservationRequest;

class ReservationController extends Controller
{
    /**
     * ReservationController constructor.
     *
     * @param ReservationService $reservationService
     */
    public function __construct(protected ReservationService $reservationService)
    {}

    /**
     * Display the reservation management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('reservation.index');
    }

    /**
     * Show the form for creating a new reservation.
     *
     * @return View
     */
    public function create(): View
    {
        return view('reservation.create');
    }

    /**
     * Get reservation statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->reservationService->getStats();
            return successResponse('Stats fetched successfully.', $stats);
        } catch (Exception $e) {
            logError('ReservationController@stats', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get reservation data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->reservationService->getDatatable();
        } catch (Exception $e) {
            logError('ReservationController@datatable', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Store a newly created reservation.
     *
     * @param StoreReservationRequest $request
     * @return JsonResponse
     */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $reservation = $this->reservationService->createReservation($validated);
            return successResponse('Reservation created successfully.', $reservation);
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('ReservationController@store', $e, ['request' => $request->all()]);
            return errorResponse('Failed to create reservation.', [$e->getMessage()]);
        }
    }

    public function showCheckInForm()
    {
        return view('reservation.check_in');
    }

    public function showCheckOutForm()
    {
        return view('reservation.check_out');
    }


    /**
     * Find a reservation by its number.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function findByNumber(Request $request): JsonResponse
    {
        $number = $request->input('reservation_number');
        try {
            $reservation = $this->reservationService->findByNumber($number);
            if (!$reservation) {
                return errorResponse('No reservation found for this Reservation Number.', [], 404);
            }
            return successResponse('Reservation found.', $reservation);
        } catch (Exception $e) {
            logError('ReservationController@findByNumber', $e, ['number' => $number]);
            return errorResponse('Failed to fetch reservation details.', [$e->getMessage()]);
        }
    }

    /**
     * Cancel the specified reservation.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function cancel($id): JsonResponse
    {
        try {
            $cancelled = $this->reservationService->cancelReservation($id);
            if (!$cancelled) {
                return errorResponse('Reservation not found or cannot be cancelled.', [], 404);
            }
            return successResponse('Reservation cancelled successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('ReservationController@cancel', $e, ['id' => $id]);
            return errorResponse('Failed to cancel reservation.', [$e->getMessage()]);
        }
    }

    /**
     * Complete (end) a reservation (checkout).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkout(Request $request): JsonResponse
    {
        try {
            $completed = $this->reservationService->completeReservation($request->all());
            if (!$completed) {
                return errorResponse('Reservation not found or cannot be completed.', [], 404);
            }
            return successResponse('Reservation completed successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('ReservationController@checkout', $e, []);
            return errorResponse('Failed to complete reservation.', [$e->getMessage()]);
        }
    }
    /**
     * Remove the specified reservation.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->reservationService->deleteReservation($id);
            if (!$deleted) {
                return errorResponse('Reservation not found.', [], 404);
            }
            return successResponse('Reservation deleted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('ReservationController@destroy', $e, ['id' => $id]);
            return errorResponse('Failed to delete reservation.', [$e->getMessage()]);
        }
    }
}
