<?php

namespace App\Http\Controllers\Housing;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\Housing\BuildingService;
use App\Models\Building;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\Housing\BuildingStoreRequest;
use App\Http\Requests\Housing\BuildingUpdateRequest;

class BuildingController extends Controller
{
    /**
     * BuildingController constructor.
     *
     * @param BuildingService $buildingService
     */
    public function __construct(protected BuildingService $buildingService)
    {}

    /**
     * Display the building management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('housing.building');
    }

    /**
     * Get building statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->buildingService->getStats();
            return successResponse('Stats fetched successfully.', $stats);
        } catch (Exception $e) {
            logError('BuildingController@stats', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get building data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->buildingService->getDatatable();
        } catch (Exception $e) {
            logError('BuildingController@datatable', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Store a newly created building.
     *
     * @param BuildingStoreRequest $request
     * @return JsonResponse
     */
    public function store(BuildingStoreRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $building = $this->buildingService->createBuilding($validated);
            return successResponse('Building created successfully.', $building);
        } catch (Exception $e) {
            logError('BuildingController@store', $e, ['request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Display the specified building.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $building = $this->buildingService->getBuilding($id);
            return successResponse('Building details fetched successfully.', $building);
        } catch (Exception $e) {
            logError('BuildingController@show', $e, ['building_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Update the specified building.
     *
     * @param BuildingUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(BuildingUpdateRequest $request, $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $building = $this->buildingService->updateBuilding(Building::findOrFail($id), $validated);
            return successResponse('Building updated successfully.', $building);
        } catch (Exception $e) {
            logError('BuildingController@update', $e, ['building_id' => $id, 'request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Remove the specified building.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->buildingService->deleteBuilding($id);
            return successResponse('Building deleted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('BuildingController@destroy', $e, ['building_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get all buildings (for dropdown and forms).
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $buildings = $this->buildingService->getAll();
            return successResponse('Buildings fetched successfully.', $buildings);
        } catch (Exception $e) {
            logError('BuildingController@all', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Activate a building (set active = true)
     * @param int $id
     * @return JsonResponse
     */
    public function activate($id): JsonResponse
    {
        try {
            $building = $this->buildingService->setActive($id, true);
            return successResponse('Building activated successfully.', $building);
        } catch (Exception $e) {
            logError('BuildingController@activate', $e, ['building_id' => $id]);
            return errorResponse('Failed to activate building.', [], 500);
        }
    }

    /**
     * Deactivate a building (set active = false)
     * @param int $id
     * @return JsonResponse
     */
    public function deactivate($id): JsonResponse
    {
        try {
            $building = $this->buildingService->setActive($id, false);
            return successResponse('Building deactivated successfully.', $building);
        } catch (Exception $e) {
            logError('BuildingController@deactivate', $e, ['building_id' => $id]);
            return errorResponse('Failed to deactivate building.', [], 500);
        }
    }
}
