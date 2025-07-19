<?php

namespace App\Services\Resident;

use App\Models\Student;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Program;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class StudentService
{
    
    /**
     * Create a new student and associated user.
     *
     * @param array $data
     * @return Student
     */
    public function createStudent(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            $user = $this->createUser($data);
            $studentData = $data;
            $studentData['user_id'] = $user->id;
            unset($studentData['password']);
            return Student::create($studentData);
        });
    }

    /**
     * Create a new user profile for the student.
     *
     * @param array $data
     * @return User
     */
    private function createUser(array $data): User
    {
        return User::create([
            'name_en' => $data['name_en'] ?? null,
            'name_ar' => $data['name_ar'] ?? null,
            'gender' => $data['gender'] ?? null,
            'email' => $data['academic_email'] ?? null,
            'password' => $data['password'] ?? bcrypt('password'),
        ]);
    }

    /**
     * Update an existing student and associated user.
     *
     * @param Student $student
     * @param array $data
     * @return Student
     */
    public function updateStudent(Student $student, array $data): Student
    {
        return DB::transaction(function () use ($student, $data) {
            $this->updateUser($student->user, $data);
            $this->updateStudentModel($student, $data);
            return $student->fresh(['user', 'program', 'faculty']);
        });
    }

    /**
     * Update the user profile for the student.
     *
     * @param User $user
     * @param array $data
     * @return void
     */
    private function updateUser(User $user, array $data): void
    {
        $user->update([
            'name_en' => $data['name_en'] ?? $user->name_en,
            'name_ar' => $data['name_ar'] ?? $user->name_ar,
            'gender' => $data['gender'] ?? $user->gender,
            'email' => $data['academic_email'] ?? $user->email,
        ]);
        if (!empty($data['password'])) {
            $user->update(['password' => bcrypt($data['password'])]);
        }
    }

    /**
     * Update the student model.
     *
     * @param Student $student
     * @param array $data
     * @return void
     */
    private function updateStudentModel(Student $student, array $data): void
    {
        $student->update($data);
    }

    /**
     * Get a single student with relationships.
     *
     * @param int $id
     * @return Student
     */
    public function getStudent($id): Student
    {
        return Student::with(['user', 'program', 'faculty', 'governorate', 'city'])->findOrFail($id);
    }

    /**
     * Delete a student and associated user.
     *
     * @param Student $student
     * @return void
     * @throws BusinessValidationException
     */
    public function deleteStudent(Student $student): void
    {
        $user = $student->user;

        if ($user) {
            $user->delete();
        }
    }

    /**
     * Get all students (for dropdowns/forms).
     *
     * @return array
     */
    public function getAll(): array
    {
        return Student::with(['user', 'program', 'faculty'])
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user?->name,
                    'academic_id' => $student->academic_id,
                    'program' => $student->program?->name,
                    'faculty' => $student->faculty?->name,
                ];
            })->toArray();
    }

    /**
     * Get student statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $totalStudents = Student::count();
        $maleStudents = Student::whereHas('user', fn($q) => $q->where('gender', 'male'))->count();
        $femaleStudents = Student::whereHas('user', fn($q) => $q->where('gender', 'female'))->count();
        $lastUpdateTime = formatDate(Student::max('updated_at'));
        $maleLastUpdate = formatDate(Student::whereHas('user', fn($q) => $q->where('gender', 'male'))->max('updated_at'));
        $femaleLastUpdate = formatDate(Student::whereHas('user', fn($q) => $q->where('gender', 'female'))->max('updated_at'));
        return [
            'total' => [
                'total' => formatNumber($totalStudents),
                'lastUpdateTime' => $lastUpdateTime
            ],
            'male' => [
                'total' => formatNumber($maleStudents),
                'lastUpdateTime' => $maleLastUpdate
            ],
            'female' => [
                'total' => formatNumber($femaleStudents),
                'lastUpdateTime' => $femaleLastUpdate
            ],
        ];
    }

    /**
     * Get student data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $query = Student::with(['user', 'program.faculty']);
        $request = request();
        $this->applySearchFilters($query, $request);
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name', fn($student) => $student->user?->name)
            ->addColumn('faculty', fn($student) => $student->program?->faculty?->name)
            ->addColumn('program', fn($student) => $student->program?->name)
            ->addColumn('gender', fn($student) => $student->user?->gender)
            ->addColumn('created_at', fn($student) => formatDate($student->created_at))
            ->addColumn('action', fn($student) => $this->renderActionButtons($student))
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Apply search filters to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    private function applySearchFilters($query, $request): void
    {
        $searchName = $request->input('search_name');
        if (!empty($searchName)) {
            $query->whereHas('user', function($q) use ($searchName) {
                $q->whereRaw('LOWER(name_en) LIKE ?', ['%' . mb_strtolower($searchName) . '%'])
                  ->orWhereRaw('LOWER(name_ar) LIKE ?', ['%' . mb_strtolower($searchName) . '%']);
            });
        }
        $facultyId = $request->input('faculty_id');
        if (!empty($facultyId)) {
            $query->where('faculty_id', $facultyId);
        }
        $programId = $request->input('program_id');
        if (!empty($programId)) {
            $query->where('program_id', $programId);
        }
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param Student $student
     * @return string
     */
    protected function renderActionButtons($student): string
    {
        return view('components.ui.datatable.data-table-actions', [
            'mode' => 'dropdown',
            'actions' => ['view','edit', 'delete'],
            'id' => $student->id,
            'type' => 'Student',
            'singleActions' => []
        ])->render();
    }
} 