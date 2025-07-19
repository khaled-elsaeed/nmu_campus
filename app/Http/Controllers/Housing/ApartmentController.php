<?php

namespace App\Http\Controllers\Housing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ApartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ApartmentController extends Controller
{
    public function __construct(protected ApartmentService $apartmentService)
    {}

    public function index(Request $request): View
    {
        return view('housing.apartment');
    }

    public function show($id): JsonResponse
    {
        try {
            $apartment = $this->apartmentService->show($id);
            if (!$apartment) {
                return errorResponse('Apartment not found', [], 404);
            }
            return successResponse('Apartment fetched successfully', $apartment);
        } catch (\Exception $e) {
            logError('ApartmentController@show', $e, ['id' => $id]);
            return errorResponse('Failed to fetch apartment', [$e->getMessage()]);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->all();
            $apartment = $this->apartmentService->update($id, $validated);
            return successResponse('Apartment updated successfully', $apartment);
        } catch (\Exception $e) {
            logError('ApartmentController@update', $e, ['id' => $id, 'request' => $request->all()]);
            return errorResponse('Failed to update apartment', [$e->getMessage()]);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->apartmentService->delete($id);
            if (!$deleted) {
                return errorResponse('Apartment not found', [], 404);
            }
            return successResponse('Apartment deleted successfully');
        } catch (\Exception $e) {
            logError('ApartmentController@destroy', $e, ['id' => $id]);
            return errorResponse('Failed to delete apartment', [$e->getMessage()]);
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = $this->apartmentService->stats();
            return successResponse('Apartment stats fetched successfully', $stats);
        } catch (\Exception $e) {
            logError('ApartmentController@stats', $e);
            return errorResponse('Failed to fetch apartment stats', [$e->getMessage()]);
        }
    }

    public function datatable(Request $request): JsonResponse
    {
        try {
            $result = $this->apartmentService->datatable($request->all());
            return $result;
        } catch (\Exception $e) {
            logError('ApartmentController@datatable', $e, ['request' => $request->all()]);
            return errorResponse('Failed to fetch apartment datatable', [$e->getMessage()]);
        }
    }

    public function all(): JsonResponse
    {
        try {
            $apartments = $this->apartmentService->list();
            return successResponse('Apartments fetched successfully', $apartments);
        } catch (Exception $e) {
            logError('ApartmentController@all', $e);
            return errorResponse('Failed to fetch apartments', [$e->getMessage()]);
        }
    }

    public function activate($id):JsonResponse
    {
        try {
            $apartment = $this->apartmentService->update($id, ['active' => true]);
            if (!$apartment) {
                return errorResponse('Apartment not found', [], 404);
            }
            return successResponse('Apartment activated successfully', $apartment);
        } catch (\Exception $e) {
            logError('ApartmentController@activate', $e, ['id' => $id]);
            return errorResponse('Failed to activate apartment', [$e->getMessage()]);
        }
    }

    public function deactivate($id): JsonResponse
    {
        try {
            $apartment = $this->apartmentService->update($id, ['active' => false]);
            if (!$apartment) {
                return errorResponse('Apartment not found', [], 404);
            }
            return successResponse('Apartment deactivated successfully', $apartment);
        } catch (\Exception $e) {
            logError('ApartmentController@deactivate', $e, ['id' => $id]);
            return errorResponse('Failed to deactivate apartment', [$e->getMessage()]);
        }
    }
}
