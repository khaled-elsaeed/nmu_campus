<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Resident\StaffService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function __construct(protected StaffService $staffService) {}

    public function index(Request $request): View
    {
        return view('residents.staff');
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->all();
            $staff = $this->staffService->create($validated);
            return successResponse('Staff created successfully', $staff);
        } catch (\Exception $e) {
            logError('StaffController@store', $e, ['request' => $request->all()]);
            return errorResponse('Failed to create staff', [$e->getMessage()]);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $staff = $this->staffService->find($id);
            if (!$staff) {
                return errorResponse('Staff not found', [], 404);
            }
            return successResponse('Staff fetched successfully', $staff);
        } catch (\Exception $e) {
            logError('StaffController@show', $e, ['id' => $id]);
            return errorResponse('Failed to fetch staff', [$e->getMessage()]);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->all();
            $staff = $this->staffService->update($id, $validated);
            return successResponse('Staff updated successfully', $staff);
        } catch (\Exception $e) {
            logError('StaffController@update', $e, ['id' => $id, 'request' => $request->all()]);
            return errorResponse('Failed to update staff', [$e->getMessage()]);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->staffService->delete($id);
            if (!$deleted) {
                return errorResponse('Staff not found', [], 404);
            }
            return successResponse('Staff deleted successfully');
        } catch (\Exception $e) {
            logError('StaffController@destroy', $e, ['id' => $id]);
            return errorResponse('Failed to delete staff', [$e->getMessage()]);
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = $this->staffService->stats();
            return successResponse('Staff stats fetched successfully', $stats);
        } catch (\Exception $e) {
            logError('StaffController@stats', $e);
            return errorResponse('Failed to fetch staff stats', [$e->getMessage()]);
        }
    }

    public function datatable(Request $request): JsonResponse
    {
        try {
            $result = $this->staffService->datatable($request->all());
            return $result;
        } catch (\Exception $e) {
            logError('StaffController@datatable', $e, ['request' => $request->all()]);
            return errorResponse('Failed to fetch staff datatable', [$e->getMessage()]);
        }
    }
} 