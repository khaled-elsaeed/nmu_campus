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
     * Display the specified reservation.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $reservation = $this->reservationService->show($id);
            if (!$reservation) {
                return errorResponse('Reservation not found.', [], 404);
            }
            return successResponse('Reservation fetched successfully.', $reservation);
        } catch (Exception $e) {
            logError('ReservationController@show', $e, ['id' => $id]);
            return errorResponse('Failed to fetch reservation.', [$e->getMessage()]);
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

    /**
     * Update the specified reservation.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'accommodation_type' => 'nullable|in:room,apartment',
                'accommodation_id' => 'nullable|integer',
                'academic_term_id' => 'nullable|exists:academic_terms,id',
                'check_in_date' => 'nullable|date',
                'check_out_date' => 'nullable|date|after:check_in_date',
                'status' => 'nullable|in:pending,confirmed,checked_in,checked_out,cancelled',
                'active' => 'nullable|boolean',
                'notes' => 'nullable|string|max:1000',
                'description' => 'nullable|string|max:1000',
            ]);

            // Validate accommodation_id based on type if both are provided
            if (isset($validated['accommodation_type']) && isset($validated['accommodation_id'])) {
                if ($validated['accommodation_type'] === 'room') {
                    $request->validate(['accommodation_id' => 'exists:rooms,id']);
                } elseif ($validated['accommodation_type'] === 'apartment') {
                    $request->validate(['accommodation_id' => 'exists:apartments,id']);
                }
            }

            $reservation = $this->reservationService->getReservation($id);
            $updatedReservation = $this->reservationService->updateReservation($reservation, $validated);
            return successResponse('Reservation updated successfully.', $updatedReservation);
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('ReservationController@update', $e, ['id' => $id, 'request' => $request->all()]);
            return errorResponse('Failed to update reservation.', [$e->getMessage()]);
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
