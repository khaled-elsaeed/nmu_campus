<?php

namespace App\Http\Controllers\Resident;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\Resident\StaffService;
use App\Models\Staff;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resident\StaffStoreRequest;
use App\Http\Requests\Resident\StaffUpdateRequest;

class StaffController extends Controller
{
    /**
     * StaffController constructor.
     *
     * @param StaffService $staffService
     */
    public function __construct(protected StaffService $staffService)
    {}

    /**
     * Display the staff management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('residents.staff');
    }

    /**
     * Get staff statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->staffService->getStats();
            return successResponse('Stats fetched successfully.', $stats);
        } catch (Exception $e) {
            logError('StaffController@stats', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get staff data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->staffService->getDatatable();
        } catch (Exception $e) {
            logError('StaffController@datatable', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Store a newly created staff member.
     *
     * @param StaffStoreRequest $request
     * @return JsonResponse
     */
    public function store(StaffStoreRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $staff = $this->staffService->createStaff($validated);
            return successResponse('Staff created successfully.', $staff);
        } catch (Exception $e) {
            logError('StaffController@store', $e, ['request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Display the specified staff member.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $staff = $this->staffService->getStaff($id);
            return successResponse('Staff details fetched successfully.', $staff);
        } catch (Exception $e) {
            logError('StaffController@show', $e, ['staff_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Update the specified staff member.
     *
     * @param StaffUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(StaffUpdateRequest $request, $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $staff = $this->staffService->updateStaff(Staff::findOrFail($id), $validated);
            return successResponse('Staff updated successfully.', $staff);
        } catch (Exception $e) {
            logError('StaffController@update', $e, ['staff_id' => $id, 'request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Remove the specified staff member.
     *
     * @param Staff $staff
     * @return JsonResponse
     */
    public function destroy(Staff $staff): JsonResponse
    {
        try {
            $this->staffService->deleteStaff($staff);
            return successResponse('Staff deleted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('StaffController@destroy', $e, ['staff_id' => $staff->id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get all staff (for dropdown and forms).
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $staff = $this->staffService->getAll();
            return successResponse('Staff fetched successfully.', $staff);
        } catch (Exception $e) {
            logError('StaffController@all', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }
} 