<?php

namespace App\Http\Controllers\Geography;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\CountryService;
use App\Models\Country;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    /**
     * CountryController constructor.
     *
     * @param CountryService $countryService
     */
    public function __construct(protected CountryService $countryService)
    {}

    /**
     * Display the country management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('countries.index');
    }

    /**
     * Get country statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->countryService->getStats();
            return successResponse('Stats fetched successfully.', $stats);
        } catch (Exception $e) {
            logError('CountryController@stats', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get country data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->countryService->getDatatable();
        } catch (Exception $e) {
            logError('CountryController@datatable', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Store a newly created country.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:countries,code',
            'name_en' => 'required|string|max:255|unique:countries,name_en',
            'name_ar' => 'nullable|string|max:255',
            'nationality_en' => 'nullable|string|max:255',
            'nationality_ar' => 'nullable|string|max:255',
        ]);

        try {
            $validated = $request->only(['code', 'name_en', 'name_ar', 'nationality_en', 'nationality_ar']);
            $country = $this->countryService->createCountry($validated);
            return successResponse('Country created successfully.', $country);
        } catch (Exception $e) {
            logError('CountryController@store', $e, ['request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Display the specified country.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $country = $this->countryService->getCountry($id);
            return successResponse('Country details fetched successfully.', $country);
        } catch (Exception $e) {
            logError('CountryController@show', $e, ['country_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Update the specified country.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:countries,code,' . $id,
            'name_en' => 'required|string|max:255|unique:countries,name_en,' . $id,
            'name_ar' => 'nullable|string|max:255',
            'nationality_en' => 'nullable|string|max:255',
            'nationality_ar' => 'nullable|string|max:255',
        ]);

        try {
            $validated = $request->only(['code', 'name_en', 'name_ar', 'nationality_en', 'nationality_ar']);
            $country = $this->countryService->updateCountry($id, $validated);
            return successResponse('Country updated successfully.', $country);
        } catch (Exception $e) {
            logError('CountryController@update', $e, ['country_id' => $id, 'request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Remove the specified country.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->countryService->deleteCountry($id);
            return successResponse('Country deleted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('CountryController@destroy', $e, ['country_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get all countries (for dropdown and forms).
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $countries = $this->countryService->getAll();
            return successResponse('Countries fetched successfully.', $countries);
        } catch (Exception $e) {
            logError('CountryController@all', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }
} 