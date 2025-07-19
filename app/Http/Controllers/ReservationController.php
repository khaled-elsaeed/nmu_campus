<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(protected ReservationService $reservationService)
    {}

    public function index(Request $request): View
    {
        return view('reservation.index');
    }

    public function show($id): JsonResponse
    {
        try {
            $reservation = $this->reservationService->show($id);
            return successResponse('Reservation fetched successfully', $reservation);
        } catch (Exception $e) {
            logError('ReservationController@show', $e, ['id' => $id]);
            return errorResponse('Failed to fetch reservation', [$e->getMessage()]);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                // Add reservation-specific validation rules here
            ]);
            $updated = $this->reservationService->update($id, $validated);
            if (!$updated) {
                return errorResponse('Reservation not found or not updated', [], 404);
            }
            return successResponse('Reservation updated successfully');
        } catch (Exception $e) {
            logError('ReservationController@update', $e, ['id' => $id, 'request' => $request->all()]);
            return errorResponse('Failed to update reservation', [$e->getMessage()]);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->reservationService->delete($id);
            if (!$deleted) {
                return errorResponse('Reservation not found', [], 404);
            }
            return successResponse('Reservation deleted successfully');
        } catch (Exception $e) {
            logError('ReservationController@destroy', $e, ['id' => $id]);
            return errorResponse('Failed to delete reservation', [$e->getMessage()]);
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = $this->reservationService->stats();
            return successResponse('Reservation stats fetched successfully', $stats);
        } catch (Exception $e) {
            logError('ReservationController@stats', $e);
            return errorResponse('Failed to fetch reservation stats', [$e->getMessage()]);
        }
    }

    public function datatable(Request $request): JsonResponse
    {
        try {
            $result = $this->reservationService->datatable($request->all());
            return $result;
        } catch (Exception $e) {
            logError('ReservationController@datatable', $e, ['request' => $request->all()]);
            return errorResponse('Failed to fetch reservation datatable', [$e->getMessage()]);
        }
    }
}
