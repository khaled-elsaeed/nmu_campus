<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\NationalityService;
use App\Models\Nationality;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;

class NationalityController extends Controller
{
    /**
     * NationalityController constructor.
     *
     * @param NationalityService $nationalityService
     */
    public function __construct(protected NationalityService $nationalityService)
    {}

    /**
     * Display the nationality management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('nationalities.index');
    }

    /**
     * Get nationality statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->nationalityService->getStats();
            return successResponse('Stats fetched successfully.', $stats);
        } catch (Exception $e) {
            logError('NationalityController@stats', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get nationality data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->nationalityService->getDatatable();
        } catch (Exception $e) {
            logError('NationalityController@datatable', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Store a newly created nationality.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:nationalities,code',
            'name_en' => 'required|string|max:255|unique:nationalities,name_en',
            'name_ar' => 'nullable|string|max:255',
        ]);

        try {
            $validated = $request->only(['code', 'name_en', 'name_ar']);
            $nationality = $this->nationalityService->createNationality($validated);
            return successResponse('Nationality created successfully.', $nationality);
        } catch (Exception $e) {
            logError('NationalityController@store', $e, ['request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Display the specified nationality.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $nationality = $this->nationalityService->getNationality($id);
            return successResponse('Nationality details fetched successfully.', $nationality);
        } catch (Exception $e) {
            logError('NationalityController@show', $e, ['nationality_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Update the specified nationality.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:nationalities,code,' . $id,
            'name_en' => 'required|string|max:255|unique:nationalities,name_en,' . $id,
            'name_ar' => 'nullable|string|max:255',
        ]);

        try {
            $validated = $request->only(['code', 'name_en', 'name_ar']);
            $nationality = $this->nationalityService->updateNationality($id, $validated);
            return successResponse('Nationality updated successfully.', $nationality);
        } catch (Exception $e) {
            logError('NationalityController@update', $e, ['nationality_id' => $id, 'request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Remove the specified nationality.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->nationalityService->deleteNationality($id);
            return successResponse('Nationality deleted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('NationalityController@destroy', $e, ['nationality_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get all nationalities (for dropdown and forms).
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $nationalities = $this->nationalityService->getAll();
            return successResponse('Nationalities fetched successfully.', $nationalities);
        } catch (Exception $e) {
            logError('NationalityController@all', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }
}