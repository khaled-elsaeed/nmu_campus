<?php

namespace App\Http\Controllers\Resident;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\Resident\StudentService;
use App\Models\Student;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resident\StudentStoreRequest;
use App\Http\Requests\Resident\StudentUpdateRequest;

class StudentController extends Controller
{
    /**
     * StudentController constructor.
     *
     * @param StudentService $studentService
     */
    public function __construct(protected StudentService $studentService)
    {}

    /**
     * Display the student management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('residents.student');
    }

    /**
     * Get student statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->studentService->getStats();
            return successResponse('Stats fetched successfully.', $stats);
        } catch (Exception $e) {
            logError('StudentController@stats', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get student data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->studentService->getDatatable();
        } catch (Exception $e) {
            logError('StudentController@datatable', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Store a newly created student.
     *
     * @param StudentStoreRequest $request
     * @return JsonResponse
     */
    public function store(StudentStoreRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $student = $this->studentService->createStudent($validated);
            return successResponse('Student created successfully.', $student);
        } catch (Exception $e) {
            logError('StudentController@store', $e, ['request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Display the specified student.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $student = $this->studentService->getStudent($id);
            return successResponse('Student details fetched successfully.', $student);
        } catch (Exception $e) {
            logError('StudentController@show', $e, ['student_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Update the specified student.
     *
     * @param StudentUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(StudentUpdateRequest $request, $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $student = $this->studentService->updateStudent(Student::findOrFail($id), $validated);
            return successResponse('Student updated successfully.', $student);
        } catch (Exception $e) {
            logError('StudentController@update', $e, ['student_id' => $id, 'request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Remove the specified student.
     *
     * @param Student $student
     * @return JsonResponse
     */
    public function destroy(Student $student): JsonResponse
    {
        try {
            $this->studentService->deleteStudent($student);
            return successResponse('Student deleted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('StudentController@destroy', $e, ['student_id' => $student->id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get all students (for dropdown and forms).
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $students = $this->studentService->getAll();
            return successResponse('Students fetched successfully.', $students);
        } catch (Exception $e) {
            logError('StudentController@all', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }
}
