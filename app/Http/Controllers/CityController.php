<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\CityService;
use App\Models\City;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;

class CityController extends Controller
{
    /**
     * CityController constructor.
     *
     * @param CityService $cityService
     */
    public function __construct(protected CityService $cityService)
    {}

    /**
     * Display the city management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('cities.index');
    }

    /**
     * Get city statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->cityService->getStats();
            return successResponse('Stats fetched successfully.', $stats);
        } catch (Exception $e) {
            logError('CityController@stats', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get city data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->cityService->getDatatable();
        } catch (Exception $e) {
            logError('CityController@datatable', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Store a newly created city.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:cities,code',
            'name_en' => 'required|string|max:255|unique:cities,name_en',
            'name_ar' => 'nullable|string|max:255',
            'governorate_id' => 'required|exists:governorates,id',
        ]);

        try {
            $validated = $request->only(['code', 'name_en', 'name_ar', 'governorate_id']);
            $city = $this->cityService->createCity($validated);
            return successResponse('City created successfully.', $city);
        } catch (Exception $e) {
            logError('CityController@store', $e, ['request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Display the specified city.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $city = $this->cityService->getCity($id);
            return successResponse('City details fetched successfully.', $city);
        } catch (Exception $e) {
            logError('CityController@show', $e, ['city_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Update the specified city.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:cities,code,' . $id,
            'name_en' => 'required|string|max:255|unique:cities,name_en,' . $id,
            'name_ar' => 'nullable|string|max:255',
            'governorate_id' => 'required|exists:governorates,id',
        ]);

        try {
            $validated = $request->only(['code', 'name_en', 'name_ar', 'governorate_id']);
            $city = $this->cityService->updateCity($id, $validated);
            return successResponse('City updated successfully.', $city);
        } catch (Exception $e) {
            logError('CityController@update', $e, ['city_id' => $id, 'request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Remove the specified city.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->cityService->deleteCity($id);
            return successResponse('City deleted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('CityController@destroy', $e, ['city_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get all cities (for dropdown and forms).
     *
     * @param string $locale
     * @param int $governorateId
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        try {
            $governorateId = $request->route('governorateId');
            $cities = $this->cityService->getAll($governorateId);
            return successResponse('Cities fetched successfully.', $cities);
        } catch (Exception $e) {
            logError('CityController@all', $e, ['governorate_id' => $governorateId]);
            return errorResponse('Internal server error.', [], 500);
        }
    }
} 