<?php

namespace App\Http\Controllers\Academic;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\Academic\FacultyService;
use App\Models\Academic\Faculty;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;

class FacultyController extends Controller
{
    /**
     * FacultyController constructor.
     *
     * @param FacultyService $facultyService
     */
    public function __construct(protected FacultyService $facultyService)
    {}

    /**
     * Display the faculty management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('academic.faculty');
    }

    /**
     * Get faculty statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->facultyService->getStats();
            return successResponse(__('Faculty statistics fetched successfully'), $stats);
        } catch (Exception $e) {
            logError('FacultyController@stats', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Get faculty data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->facultyService->getDatatable();
        } catch (Exception $e) {
            logError('FacultyController@datatable', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Store a newly created faculty.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name_en' => 'required|string|max:255|unique:faculties,name_en',
            'name_ar' => 'required|string|max:255|unique:faculties,name_ar',
        ]);

        try {
            $validated = $request->only(['name_en', 'name_ar']);
            $faculty = $this->facultyService->createFaculty($validated);
            return successResponse(__('Faculty created successfully'), $faculty);
        } catch (Exception $e) {
            logError('FacultyController@store', $e, ['request' => $request->all()]);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Display the specified faculty.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $faculty = $this->facultyService->getFaculty($id);
            return successResponse(__('Faculty details fetched successfully'), $faculty);
        } catch (Exception $e) {
            logError('FacultyController@show', $e, ['faculty_id' => $id]);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Update the specified faculty.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name_en' => 'required|string|max:255|unique:faculties,name_en,' . $id,
            'name_ar' => 'required|string|max:255|unique:faculties,name_ar,' . $id,
        ]);

        try {
            $validated = $request->only(['name_en', 'name_ar']);
            $faculty = $this->facultyService->updateFaculty($id, $validated);
            return successResponse(__('Faculty updated successfully'), $faculty);
        } catch (Exception $e) {
            logError('FacultyController@update', $e, ['faculty_id' => $id, 'request' => $request->all()]);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Remove the specified faculty.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->facultyService->deleteFaculty($id);
            return successResponse(__('Faculty deleted successfully'));
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('FacultyController@destroy', $e, ['faculty_id' => $id]);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Get all faculties (for dropdown and forms).
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $faculties = $this->facultyService->getAll();
            return successResponse(__('Faculties fetched successfully'), $faculties);
        } catch (Exception $e) {
            logError('FacultyController@all', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }
}