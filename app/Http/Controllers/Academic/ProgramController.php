<?php

namespace App\Http\Controllers\Academic;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\Academic\ProgramService;
use App\Models\Academic\Program;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;

class ProgramController extends Controller
{
    /**
     * ProgramController constructor.
     *
     * @param ProgramService $programService
     */
    public function __construct(protected ProgramService $programService)
    {}

    /**
     * Display the program management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('academic.program');
    }

    /**
     * Get program statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->programService->getStats();
            return successResponse(__('Program statistics fetched successfully'), $stats);
        } catch (Exception $e) {
            logError('ProgramController@stats', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Get program data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->programService->getDatatable();
        } catch (Exception $e) {
            logError('ProgramController@datatable', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Store a newly created program.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name_en' => 'required|string|max:255|unique:programs,name_en',
            'name_ar' => 'required|string|max:255|unique:programs,name_ar',
            'faculty_id' => 'required|exists:faculties,id',
            'duration_years' => 'required|integer|min:1|max:10',
        ]);

        try {
            $validated = $request->only(['name_en', 'name_ar', 'faculty_id', 'duration_years']);
            $program = $this->programService->createProgram($validated);
            return successResponse(__('Program created successfully'), $program);
        } catch (Exception $e) {
            logError('ProgramController@store', $e, ['request' => $request->all()]);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Display the specified program.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $program = $this->programService->getProgram($id);
            return successResponse(__('Program details fetched successfully'), $program);
        } catch (Exception $e) {
            logError('ProgramController@show', $e, ['program_id' => $id]);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Update the specified program.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name_en' => 'required|string|max:255|unique:programs,name_en,' . $id,
            'name_ar' => 'required|string|max:255|unique:programs,name_ar,' . $id,
            'faculty_id' => 'required|exists:faculties,id',
            'duration_years' => 'required|integer|min:1|max:10',
        ]);

        try {
            $validated = $request->only(['name_en', 'name_ar', 'faculty_id', 'duration_years']);
            $program = $this->programService->updateProgram($id, $validated);
            return successResponse(__('Program updated successfully'), $program);
        } catch (Exception $e) {
            logError('ProgramController@update', $e, ['program_id' => $id, 'request' => $request->all()]);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Remove the specified program.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->programService->deleteProgram($id);
            return successResponse(__('Program deleted successfully'));
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('ProgramController@destroy', $e, ['program_id' => $id]);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    /**
     * Get all programs by faculty.
     * @param int $id
     * @return JsonResponse
     */
    public function all($id): JsonResponse
    {
        try {
            $programs = $this->programService->getAll($id);
            return successResponse(__('Programs fetched successfully'), $programs);
        } catch (Exception $e) {
            logError('ProgramController@all', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }
}