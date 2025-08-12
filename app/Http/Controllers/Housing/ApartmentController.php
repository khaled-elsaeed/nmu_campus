<?php

namespace App\Http\Controllers\Housing;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\Housing\ApartmentService;
use App\Models\Housing\Apartment;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;

class ApartmentController extends Controller
{
    /**
     * ApartmentController constructor.
     *
     * @param ApartmentService $apartmentService
     */
    public function __construct(protected ApartmentService $apartmentService)
    {}

    /**
     * Display the apartment management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('housing.apartment');
    }

    /**
     * Get apartment statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->apartmentService->getStats();
            return successResponse(__('Apartments statistics fetched successfully'), $stats);
        } catch (Exception $e) {
            logError('ApartmentController@stats', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Get apartment data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->apartmentService->getDatatable();
        } catch (Exception $e) {
            logError('ApartmentController@datatable', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Display the specified apartment.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $apartment = $this->apartmentService->getApartment($id);
            return successResponse(__('Apartment details fetched successfully'), $apartment);
        } catch (Exception $e) {
            logError('ApartmentController@show', $e, ['apartment_id' => $id]);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }


    /**
     * Remove the specified apartment.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->apartmentService->deleteApartment($id);
            return successResponse(__('Apartment deleted successfully'));
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('ApartmentController@destroy', $e, ['apartment_id' => $id]);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Activate an apartment (set active = true)
     * @param int $id
     * @return JsonResponse
     */
    public function activate($id): JsonResponse
    {
        try {
            $this->apartmentService->setActive($id, true);
            return successResponse(__('Apartment activated successfully'));
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('ApartmentController@activate', $e, ['apartment_id' => $id]);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Deactivate an apartment (set active = false)
     * @param int $id
     * @return JsonResponse
     */
    public function deactivate($id): JsonResponse
    {
        try {
            $this->apartmentService->setActive($id, false);
            return successResponse(__('Apartment deactivated successfully'));
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('ApartmentController@deactivate', $e, ['apartment_id' => $id]);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Get all apartments for a specific building.
     * @param int $id
     * @return JsonResponse
     */
    public function all($buildingId): JsonResponse
    {
        try {
            $apartments = $this->apartmentService->getAll($buildingId);
            return successResponse(__('Apartments fetched successfully'), $apartments);
        } catch (Exception $e) {
            logError('ApartmentController@all', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }
}
