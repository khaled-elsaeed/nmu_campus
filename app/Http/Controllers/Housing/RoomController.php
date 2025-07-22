<?php

namespace App\Http\Controllers\Housing;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\Housing\RoomService;
use App\Models\Room;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;

class RoomController extends Controller
{
    /**
     * RoomController constructor.
     *
     * @param RoomService $roomService
     */
    public function __construct(protected RoomService $roomService)
    {}

    /**
     * Display the room management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('housing.room');
    }

    /**
     * Get room statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->roomService->getStats();
            return successResponse('Stats fetched successfully.', $stats);
        } catch (Exception $e) {
            logError('RoomController@stats', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get room data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->roomService->getDatatable();
        } catch (Exception $e) {
            logError('RoomController@datatable', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }


    /**
     * Display the specified room.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $room = $this->roomService->getRoom($id);
            return successResponse('Room details fetched successfully.', $room);
        } catch (Exception $e) {
            logError('RoomController@show', $e, ['room_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }


    /**
     * Remove the specified room.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->roomService->deleteRoom($id);
            return successResponse('Room deleted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('RoomController@destroy', $e, ['room_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Update the specified room.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = $request->only([
                'type',
                'purpose',
                'description',
            ]);
            $updatedRoom = $this->roomService->updateRoom($id, $data);
            return successResponse('Room updated successfully.', $updatedRoom);
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('RoomController@update', $e, ['room_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get all rooms (for dropdown and forms).
     * Optionally filter by apartment_id.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        try {
            $apartmentId = $request->query('apartment_id');
            $rooms = $this->roomService->getAll($apartmentId);
            return successResponse('Rooms fetched successfully.', $rooms);
        } catch (Exception $e) {
            logError('RoomController@all', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Activate a room (set active = true)
     * @param int $id
     * @return JsonResponse
     */
    public function activate($id): JsonResponse
    {
        try {
            $room = $this->roomService->setActive($id, true);
            return successResponse('Room activated successfully.', $room);
        } catch (Exception $e) {
            logError('RoomController@activate', $e, ['room_id' => $id]);
            return errorResponse('Failed to activate room.', [], 500);
        }
    }

    /**
     * Deactivate a room (set active = false)
     * @param int $id
     * @return JsonResponse
     */
    public function deactivate($id): JsonResponse
    {
        try {
            $room = $this->roomService->setActive($id, false);
            return successResponse('Room deactivated successfully.', $room);
        } catch (Exception $e) {
            logError('RoomController@deactivate', $e, ['room_id' => $id]);
            return errorResponse('Failed to deactivate room.', [], 500);
        }
    }
}
