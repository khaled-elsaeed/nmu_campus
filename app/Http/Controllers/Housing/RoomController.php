<?php

namespace App\Http\Controllers\Housing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function __construct(protected RoomService $roomService)
    {}

    public function index(Request $request): View
    {
        return view('housing.room');
    }

    public function show($id): JsonResponse
    {
        try {
            $room = $this->roomService->show($id);
            return successResponse('Room fetched successfully', $room);
        } catch (Exception $e) {
            logError('RoomController@show', $e, ['id' => $id]);
            return errorResponse('Failed to fetch room', [$e->getMessage()]);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:single,double',
                'purpose' => 'required|in:housing,staff_housing,office,storage',
                'description' => 'nullable|string',
            ]);
            $updated = $this->roomService->update($id, $validated);
            if (!$updated) {
                return errorResponse('Room not found or not updated', [], 404);
            }
            return successResponse('Room updated successfully');
        } catch (Exception $e) {
            logError('RoomController@update', $e, ['id' => $id, 'request' => $request->all()]);
            return errorResponse('Failed to update room', [$e->getMessage()]);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->roomService->delete($id);
            if (!$deleted) {
                return errorResponse('Room not found', [], 404);
            }
            return successResponse('Room deleted successfully');
        } catch (Exception $e) {
            logError('RoomController@destroy', $e, ['id' => $id]);
            return errorResponse('Failed to delete room', [$e->getMessage()]);
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = $this->roomService->stats();
            return successResponse('Room stats fetched successfully', $stats);
        } catch (Exception $e) {
            logError('RoomController@stats', $e);
            return errorResponse('Failed to fetch room stats', [$e->getMessage()]);
        }
    }

    public function datatable(Request $request): JsonResponse
    {
        try {
            $result = $this->roomService->datatable($request->all());
            return $result;
        } catch (Exception $e) {
            logError('RoomController@datatable', $e, ['request' => $request->all()]);
            return errorResponse('Failed to fetch room datatable', [$e->getMessage()]);
        }
    }

    public function activate($id): JsonResponse
    {
        try {
            $room = $this->roomService->update($id, ['active' => true]);
            if (!$room) {
                return errorResponse('Room not found', [], 404);
            }
            return successResponse('Room activated successfully', $room);
        } catch (Exception $e) {
            logError('RoomController@activate', $e, ['id' => $id]);
            return errorResponse('Failed to activate room', [$e->getMessage()]);
        }
    }

    public function deactivate($id): JsonResponse
    {
        try {
            $room = $this->roomService->update($id, ['active' => false]);
            if (!$room) {
                return errorResponse('Room not found', [], 404);
            }
            return successResponse('Room deactivated successfully', $room);
        } catch (Exception $e) {
            logError('RoomController@deactivate', $e, ['id' => $id]);
            return errorResponse('Failed to deactivate room', [$e->getMessage()]);
        }
    }

    public function all(Request $request): JsonResponse
    {
        try {
            $apartmentId = $request->query('apartment_id');
            $rooms = $this->roomService->all($apartmentId);
            return successResponse('Rooms fetched successfully', $rooms);
        } catch (\Exception $e) {
            logError('RoomController@all', $e);
            return errorResponse('Failed to fetch rooms', [$e->getMessage()]);
        }
    }
}
