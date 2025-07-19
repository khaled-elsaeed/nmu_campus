<?php

namespace App\Http\Controllers\Housing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BuildingService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Http\Requests\Housing\BuildingStoreRequest;
use App\Http\Requests\Housing\BuildingUpdateRequest;

class BuildingController extends Controller
{

    public function __construct(protected BuildingService $buildingService)
    {}

    public function index(Request $request): View
    {
        return view('housing.building');
    }

    public function store(BuildingStoreRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $building = $this->buildingService->create($validated);
            return successResponse('Building created successfully', $building);
        } catch (Exception $e) {
            logError('BuildingController@store', $e, ['request' => $request->all()]);
            return errorResponse('Failed to create building', [$e->getMessage()]);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $building = $this->buildingService->find($id);
            if (!$building) {
                return errorResponse('Building not found', [], 404);
            }
            return successResponse('Building fetched successfully', $building);
        } catch (Exception $e) {
            logError('BuildingController@show', $e, ['id' => $id]);
            return errorResponse('Failed to fetch building', [$e->getMessage()]);
        }
    }

    public function update(BuildingUpdateRequest $request, $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $building = $this->buildingService->update($id, $validated);
            return successResponse('Building updated successfully', $building);
        } catch (Exception $e) {
            logError('BuildingController@update', $e, ['id' => $id, 'request' => $request->all()]);
            return errorResponse('Failed to update building', [$e->getMessage()]);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->buildingService->delete($id);
            if (!$deleted) {
                return errorResponse('Building not found', [], 404);
            }
            return successResponse('Building deleted successfully');
        } catch (Exception $e) {
            logError('BuildingController@destroy', $e, ['id' => $id]);
            return errorResponse('Failed to delete building', [$e->getMessage()]);
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = $this->buildingService->stats();
            return successResponse('Building stats fetched successfully', $stats);
        } catch (Exception $e) {
            logError('BuildingController@stats', $e);
            return errorResponse('Failed to fetch building stats', [$e->getMessage()]);
        }
    }

    /**
     * Handle the datatable AJAX request for buildings.
     *
     * This method delegates to the service, which returns the JSON structure
     * expected by DataTables.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function datatable(Request $request): JsonResponse
    {
        try {
            $result = $this->buildingService->datatable($request->all());
            return $result;
        } catch (Exception $e) {
            logError('BuildingController@datatable', $e, ['request' => $request->all()]);
            return errorResponse('Failed to fetch building datatable', [$e->getMessage()]);
        }
    }

    public function all(): JsonResponse
    {
        try {
            $buildings = $this->buildingService->list();
            return successResponse('Buildings fetched successfully', $buildings);
        } catch (Exception $e) {
            logError('BuildingController@all', $e);
            return errorResponse('Failed to fetch buildings', [$e->getMessage()]);
        }
    }

    public function activate($id): JsonResponse
    {
        try {
            $building = $this->buildingService->update($id, ['active' => true]);
            if (!$building) {
                return errorResponse('Building not found', [], 404);
            }
            return successResponse('Building activated successfully', $building);
        } catch (\Exception $e) {
            logError('BuildingController@activate', $e, ['id' => $id]);
            return errorResponse('Failed to activate building', [$e->getMessage()]);
        }
    }

    public function deactivate($id): JsonResponse
    {
        try {
            $building = $this->buildingService->update($id, ['active' => false]);
            if (!$building) {
                return errorResponse('Building not found', [], 404);
            }
            return successResponse('Building deactivated successfully', $building);
        } catch (\Exception $e) {
            logError('BuildingController@deactivate', $e, ['id' => $id]);
            return errorResponse('Failed to deactivate building', [$e->getMessage()]);
        }
    }
}
