<?php

namespace App\Http\Controllers\Reservation;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\Reservation\Request\ReservationRequestService;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\Request\UpdateReservationRequest;
use Illuminate\Support\Facades\DB;

class ReservationRequestController extends Controller
{
    /**
     * ReservationRequestController constructor.
     *
     * @param ReservationRequestService $reservationRequestService
     */
    public function __construct(protected ReservationRequestService $reservationRequestService)
    {}

    /**
     * Display the reservation management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('reservation.request.index');
    }


    /**
     * Get reservation statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->reservationRequestService->getStats();
            return successResponse('Stats fetched successfully.', $stats);
        } catch (Exception $e) {
            logError('ReservationRequestController@stats', $e);
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
            return $this->reservationRequestService->getDatatable();
        } catch (Exception $e) {
            logError('ReservationRequestController@datatable', $e);
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
            $reservation = $this->reservationRequestService->show($id);
            if (!$reservation) {
                return errorResponse('Reservation not found.', [], 404);
            }
            return successResponse('Reservation fetched successfully.', $reservation);
        } catch (Exception $e) {
            logError('ReservationRequestController@show', $e, ['id' => $id]);
            return errorResponse('Failed to fetch reservation.', [$e->getMessage()]);
        }
    }


    /**
     * Update the specified reservation.
     *
     * @param UpdateReservationRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateReservationRequest $request, $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $reservation = $this->reservationRequestService->getReservation($id);
            $updatedReservation = $this->reservationRequestService->updateReservation($reservation, $validated);
            return successResponse('Reservation updated successfully.', $updatedReservation);
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('ReservationRequestController@update', $e, ['id' => $id, 'request' => $request->all()]);
            return errorResponse('Failed to update reservation.', [$e->getMessage()]);
        }
    }

    public function accept(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate(
                [   'accommodation_type' => 'required|string|in:room,apartment',
                    'accommodation_id' => [
                        'required',
                        'integer',
                        function ($attribute, $value, $fail) use ($request) {
                            $type = $request->input('accommodation_type');

                            if ($type === 'room') {
                                if (!DB::table('rooms')->where('id', $value)->exists()) {
                                    return $fail(__('The selected room does not exist.'));
                                }
                            } elseif ($type === 'apartment') {
                                if (!DB::table('apartments')->where('id', $value)->exists()) {
                                    return $fail(__('The selected apartment does not exist.'));
                                }
                            } else {
                                return $fail(__('Invalid accommodation type.'));
                            }
                        },
                    ],
                    'bed_count' => 'nullable|string|max:255',
                    'notes' => 'nullable|string|max:1000',

                ]
            );
            $this->reservationRequestService->acceptRequest($validated, $id);
            return successResponse('Reservation request accepted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('ReservationRequestController@accept', $e, ['id' => $id]);
            return errorResponse('Failed to accept reservation request.', [$e->getMessage()]);
        }
    }

    public function cancel($id): JsonResponse
    {
        try {
            $this->reservationRequestService->cancelRequest($id);
            return successResponse('Reservation request canceled successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('ReservationRequestController@cancel', $e, ['id' => $id]);
            return errorResponse('Failed to cancel reservation request.', [$e->getMessage()]);
        }
    }
}
