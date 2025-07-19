<?php

namespace App\Http\Controllers\Academic;

use Illuminate\Http\{JsonResponse, Request};
use Illuminate\View\View;
use App\Services\Academic\ProgramService;
use App\Models\Program;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\ProgramStoreRequest;
use App\Http\Requests\Academic\ProgramUpdateRequest;

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
            return successResponse('Stats fetched successfully.', $stats);
        } catch (Exception $e) {
            logError('ProgramController@stats', $e);
            return errorResponse('Internal server error.', [], 500);
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
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Store a newly created program.
     *
     * @param ProgramStoreRequest $request
     * @return JsonResponse
     */
    public function store(ProgramStoreRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $program = $this->programService->createProgram($validated);
            return successResponse('Program created successfully.', $program);
        } catch (Exception $e) {
            logError('ProgramController@store', $e, ['request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
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
            return successResponse('Program details fetched successfully.', $program);
        } catch (Exception $e) {
            logError('ProgramController@show', $e, ['program_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Update the specified program.
     *
     * @param ProgramUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ProgramUpdateRequest $request, $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $program = $this->programService->updateProgram(\App\Models\Program::findOrFail($id), $validated);
            return successResponse('Program updated successfully.', $program);
        } catch (Exception $e) {
            logError('ProgramController@update', $e, ['program_id' => $id, 'request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Remove the specified program.
     *
     * @param Program $program
     * @return JsonResponse
     */
    public function destroy(Program $program): JsonResponse
    {
        try {
            $this->programService->deleteProgram($program);
            return successResponse('Program deleted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('ProgramController@destroy', $e, ['program_id' => $program->id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get all programs (for dropdown and forms).
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $programs = $this->programService->getAll();
            return successResponse('Programs fetched successfully.', $programs);
        } catch (Exception $e) {
            logError('ProgramController@all', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get all faculties for dropdown.
     *
     * @return JsonResponse
     */
    public function getFaculties(): JsonResponse
    {
        try {
            $faculties = $this->programService->getFaculties();
            return successResponse('Faculties fetched successfully.', $faculties);
        } catch (Exception $e) {
            logError('ProgramController@getFaculties', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }
} 