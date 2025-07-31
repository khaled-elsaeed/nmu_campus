<?php

namespace App\Http\Controllers\Reservation;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\Reservation\MyReservationService;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\StoreReservationRequest;

class MyReservationController extends Controller
{
    /**
     * MyReservationController constructor.
     *
     * @param MyReservationService $myReservationService
     */
    public function __construct(protected MyReservationService $myReservationService)
    {}

    /**
     * Display the user's reservations as cards.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {

        return view('reservation.my-reservations');
    }


    /**
     * Get reservation data for cards (for AJAX card rendering/filtering).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function cardData(Request $request): JsonResponse
    {
        try {
            $filters = [
                'property' => $request->input('property'),
                'status' => $request->input('status'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
            ];
            $reservations = $this->myReservationService->getUserReservations(auth()->id(), $filters);
            return successResponse('Reservations fetched successfully.', $reservations);
        } catch (Exception $e) {
            logError('MyReservationController@cardData', $e);
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
            $reservation = $this->myReservationService->createReservation($validated);
            return successResponse('Reservation created successfully.', $reservation);
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('MyReservationController@store', $e, ['request' => $request->all()]);
            return errorResponse('Failed to create reservation.', [$e->getMessage()]);
        }
    }

    /**
     * Cancel the specified reservation (for the current user).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function cancel($id): JsonResponse
    {
        try {
            $cancelled = $this->myReservationService->cancelUserReservation(auth()->id(), $id);
            if (!$cancelled) {
                return errorResponse('Reservation not found or cannot be cancelled.', [], 404);
            }
            return successResponse('Reservation cancelled successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('MyReservationController@cancel', $e, ['id' => $id]);
            return errorResponse('Failed to cancel reservation.', [$e->getMessage()]);
        }
    }

    /**
     * Complete (end) a reservation (checkout) for the current user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkout(Request $request): JsonResponse
    {
        try {
            $completed = $this->myReservationService->completeUserReservation(auth()->id(), $request->all());
            if (!$completed) {
                return errorResponse('Reservation not found or cannot be completed.', [], 404);
            }
            return successResponse('Reservation completed successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('MyReservationController@checkout', $e, []);
            return errorResponse('Failed to complete reservation.', [$e->getMessage()]);
        }
    }

    /**
     * Remove the specified reservation (for the current user).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->myReservationService->deleteUserReservation(auth()->id(), $id);
            if (!$deleted) {
                return errorResponse('Reservation not found.', [], 404);
            }
            return successResponse('Reservation deleted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('MyReservationController@destroy', $e, ['id' => $id]);
            return errorResponse('Failed to delete reservation.', [$e->getMessage()]);
        }
    }
}
