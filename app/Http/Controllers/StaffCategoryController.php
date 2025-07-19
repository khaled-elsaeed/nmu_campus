<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StaffCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class StaffCategoryController extends Controller
{
    public function __construct(protected StaffCategoryService $service) {}

    public function index(Request $request): View
    {
        return view('staff-category.index');
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->all();
            $cat = $this->service->create($validated);
            return successResponse('Staff category created successfully', $cat);
        } catch (\Exception $e) {
            logError('StaffCategoryController@store', $e, ['request' => $request->all()]);
            return errorResponse('Failed to create staff category', [$e->getMessage()]);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $cat = $this->service->find($id);
            if (!$cat) {
                return errorResponse('Staff category not found', [], 404);
            }
            return successResponse('Staff category fetched successfully', $cat);
        } catch (\Exception $e) {
            logError('StaffCategoryController@show', $e, ['id' => $id]);
            return errorResponse('Failed to fetch staff category', [$e->getMessage()]);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->all();
            $cat = $this->service->update($id, $validated);
            return successResponse('Staff category updated successfully', $cat);
        } catch (\Exception $e) {
            logError('StaffCategoryController@update', $e, ['id' => $id, 'request' => $request->all()]);
            return errorResponse('Failed to update staff category', [$e->getMessage()]);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->service->delete($id);
            if (!$deleted) {
                return errorResponse('Staff category not found', [], 404);
            }
            return successResponse('Staff category deleted successfully');
        } catch (\Exception $e) {
            logError('StaffCategoryController@destroy', $e, ['id' => $id]);
            return errorResponse('Failed to delete staff category', [$e->getMessage()]);
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = $this->service->stats();
            return successResponse('Staff category stats fetched successfully', $stats);
        } catch (\Exception $e) {
            logError('StaffCategoryController@stats', $e);
            return errorResponse('Failed to fetch staff category stats', [$e->getMessage()]);
        }
    }

    public function datatable(Request $request): JsonResponse
    {
        try {
            $result = $this->service->datatable($request->all());
            return $result;
        } catch (\Exception $e) {
            logError('StaffCategoryController@datatable', $e, ['request' => $request->all()]);
            return errorResponse('Failed to fetch staff category datatable', [$e->getMessage()]);
        }
    }

    public function all(): JsonResponse
    {
        try {
            $categories = $this->service->getAll();
            return successResponse('Staff categories fetched successfully.', $categories);
        } catch (\Exception $e) {
            logError('StaffCategoryController@all', $e);
            return errorResponse('Failed to fetch staff categories.', [], 500);
        }
    }
} 