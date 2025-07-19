<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DepartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(protected DepartmentService $service) {}

    public function index(Request $request): View
    {
        return view('department.index');
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->all();
            $dep = $this->service->create($validated);
            return successResponse('Department created successfully', $dep);
        } catch (\Exception $e) {
            logError('DepartmentController@store', $e, ['request' => $request->all()]);
            return errorResponse('Failed to create department', [$e->getMessage()]);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $dep = $this->service->find($id);
            if (!$dep) {
                return errorResponse('Department not found', [], 404);
            }
            return successResponse('Department fetched successfully', $dep);
        } catch (\Exception $e) {
            logError('DepartmentController@show', $e, ['id' => $id]);
            return errorResponse('Failed to fetch department', [$e->getMessage()]);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->all();
            $dep = $this->service->update($id, $validated);
            return successResponse('Department updated successfully', $dep);
        } catch (\Exception $e) {
            logError('DepartmentController@update', $e, ['id' => $id, 'request' => $request->all()]);
            return errorResponse('Failed to update department', [$e->getMessage()]);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->service->delete($id);
            if (!$deleted) {
                return errorResponse('Department not found', [], 404);
            }
            return successResponse('Department deleted successfully');
        } catch (\Exception $e) {
            logError('DepartmentController@destroy', $e, ['id' => $id]);
            return errorResponse('Failed to delete department', [$e->getMessage()]);
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = $this->service->stats();
            return successResponse('Department stats fetched successfully', $stats);
        } catch (\Exception $e) {
            logError('DepartmentController@stats', $e);
            return errorResponse('Failed to fetch department stats', [$e->getMessage()]);
        }
    }

    public function datatable(Request $request): JsonResponse
    {
        try {
            $result = $this->service->datatable($request->all());
            return $result;
        } catch (\Exception $e) {
            logError('DepartmentController@datatable', $e, ['request' => $request->all()]);
            return errorResponse('Failed to fetch department datatable', [$e->getMessage()]);
        }
    }

    public function all(): JsonResponse
    {
        try {
            $departments = $this->service->getAll();
            return successResponse('Departments fetched successfully.', $departments);
        } catch (\Exception $e) {
            logError('DepartmentController@all', $e);
            return errorResponse('Failed to fetch departments.', [], 500);
        }
    }
} 