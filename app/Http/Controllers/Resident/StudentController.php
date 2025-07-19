<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Resident\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
// Optionally: use App\Http\Requests\StudentStoreRequest;
// Optionally: use App\Http\Requests\StudentUpdateRequest;

class StudentController extends Controller
{
    public function __construct(protected StudentService $studentService) {}

    public function index(Request $request): View
    {
        return view('residents.student');
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->all(); // Replace with $request->validated() if using FormRequest
            $student = $this->studentService->create($validated);
            return successResponse('Student created successfully', $student);
        } catch (\Exception $e) {
            logError('StudentController@store', $e, ['request' => $request->all()]);
            return errorResponse('Failed to create student', [$e->getMessage()]);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $student = $this->studentService->find($id);
            if (!$student) {
                return errorResponse('Student not found', [], 404);
            }
            return successResponse('Student fetched successfully', $student);
        } catch (\Exception $e) {
            logError('StudentController@show', $e, ['id' => $id]);
            return errorResponse('Failed to fetch student', [$e->getMessage()]);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->all(); // Replace with $request->validated() if using FormRequest
            $student = $this->studentService->update($id, $validated);
            return successResponse('Student updated successfully', $student);
        } catch (\Exception $e) {
            logError('StudentController@update', $e, ['id' => $id, 'request' => $request->all()]);
            return errorResponse('Failed to update student', [$e->getMessage()]);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->studentService->delete($id);
            if (!$deleted) {
                return errorResponse('Student not found', [], 404);
            }
            return successResponse('Student deleted successfully');
        } catch (\Exception $e) {
            logError('StudentController@destroy', $e, ['id' => $id]);
            return errorResponse('Failed to delete student', [$e->getMessage()]);
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $stats = $this->studentService->stats();
            return successResponse('Student stats fetched successfully', $stats);
        } catch (Exception $e) {
            logError('StudentController@stats', $e);
            return errorResponse('Failed to fetch student stats', [$e->getMessage()]);
        }
    }


    public function datatable(Request $request): JsonResponse
    {
        try {
            $result = $this->studentService->datatable($request->all());
            return $result;
        } catch (\Exception $e) {
            logError('StudentController@datatable', $e, ['request' => $request->all()]);
            return errorResponse('Failed to fetch student datatable', [$e->getMessage()]);
        }
    }
}
