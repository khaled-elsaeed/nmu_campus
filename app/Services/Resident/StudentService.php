<?php

namespace App\Services\Resident;

use App\Models\Resident\Student;
use App\Models\User;
use App\Models\Academic\Faculty;
use App\Models\Academic\Program;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Notifications\AccountCreated;

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
            $passwordData = $this->generatePassword();
            $user = $this->createUser($data, $passwordData['hashed']);
            $data['user_id'] = $user->id;
            $student = Student::create($data);
            $user->notify((new AccountCreated($student, $passwordData['plain']))->afterCommit());
            return $student;
        });
    }

    /**
     * Create a new user profile for the student.
     *
     * @param array $data
     * @param string $hashedPassword
     * @return User
     */
    private function createUser(array $data, string $hashedPassword): User
    {
        return User::create([
            'name_en' => $data['name_en'] ?? null,
            'name_ar' => $data['name_ar'] ?? null,
            'gender' => $data['gender'] ?? null,
            'email' => $data['academic_email'] ?? null,
            'password' => $hashedPassword,
        ]);
    }

    /**
     * Generate a password for the student.
     *
     * @return array
     */
    private function generatePassword(): array
    {
        $plain = Str::password(length: 12);
        return [
            'plain' => $plain,
            'hashed' => Hash::make($plain),
        ];
    }


    /**
     * Update an existing student and associated user by student ID.
     *
     * @param int $studentId
     * @param array $data
     * @return Student
     */
    public function updateStudent(int $studentId, array $data): Student
    {
        return DB::transaction(function () use ($studentId, $data) {
            $student = Student::findOrFail($studentId);
            $this->updateUser($student->user, $data);
            $this->updateStudentProfile($student, $data);
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
    }

    /**
     * Update the student model.
     *
     * @param Student $student
     * @param array $data
     * @return void
     */
    private function updateStudentProfile(Student $student, array $data): void
    {
        $student->update($data);
    }

    /**
     * Get a single student with selected fields and relationships, including program, faculty, governorate, and city names.
     *
     * @param int $id
     * @return array
     */
    public function getStudent(int $id): array
    {
        $student = Student::select([
                'id',
                'user_id',
                'name_en',
                'name_ar',
                'academic_id',
                'national_id',
                'faculty_id',
                'program_id',
                'level',
                'nationality_id',
                'governorate_id',
                'city_id',
                'phone',
                'academic_email',
                'street',
                'date_of_birth',
            ])
            ->with([
                'user:id,gender',
                'program:id,name_en,name_ar',
                'faculty:id,name_en,name_ar',
                'nationality:id,name_en,name_ar',
                'governorate:id,name_en,name_ar',
                'city:id,name_en,name_ar',
            ])
            ->find($id);

        if (!$student) {
            throw new BusinessValidationException(__(':field not found.', ['field' => 'Student']));
        }

        return [
            'id' => $student->id,
            'academic_id' => $student->academic_id,
            'national_id' => $student->national_id,
            'faculty_id' => $student->faculty_id,
            'faculty' => $student->faculty->name?? null,
            'program_id' => $student->program_id,
            'program' => $student->program->name ?? null,
            'nationality_id' => $student->nationality_id,
            'nationality' => $student->nationality->name ?? null,
            'governorate_id' => $student->governorate_id,
            'governorate' => $student->governorate->name ?? null,
            'city_id' => $student->city_id,
            'city' => $student->city->name ?? null,
            'phone' => $student->phone,
            'academic_email' => $student->academic_email,
            'street' => $student->street,
            'date_of_birth' => $student->date_of_birth,
            'level' => $student->level,
            'name_en' => $student->name_en,
            'name_ar' => $student->name_ar ?? null,
            'gender' => $student->user->gender ?? null,
        ];
    }

    /**
     * Delete a student and associated user.
     *
     * @param int $id
     * @return bool
     */
    public function deleteStudent(int $id): bool
    {
        $student = Student::findOrFail($id);
        $user = $student->user;
        return (bool) $user->delete();
    }

    /**
     * Get all students.
     *
     * @return array
     */
    public function getAll(): array
    {
        return Student::with('user:id,name_en,name_ar')->get(['id', 'user_id'])->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->user->name,
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
        $stats = Student::join('users', 'students.user_id', '=', 'users.id')
            ->selectRaw('
                COUNT(*) AS total_count,
                SUM(CASE WHEN users.gender = "male" THEN 1 ELSE 0 END) AS male_count,
                SUM(CASE WHEN users.gender = "female" THEN 1 ELSE 0 END) AS female_count,
                MAX(students.updated_at) AS last_update_time,
                MAX(CASE WHEN users.gender = "male" THEN students.updated_at ELSE NULL END) AS male_last_update,
                MAX(CASE WHEN users.gender = "female" THEN students.updated_at ELSE NULL END) AS female_last_update
            ')
            ->first();

        return [
            'students' => [
                'count' => formatNumber($stats->total_count ?? 0),
                'lastUpdateTime' => formatDate($stats->last_update_time ?? null)
            ],
            'students-male' => [
                'count' => formatNumber($stats->male_count ?? 0),
                'lastUpdateTime' => formatDate($stats->male_last_update ?? null)
            ],
            'students-female' => [
                'count' => formatNumber($stats->female_count ?? 0),
                'lastUpdateTime' => formatDate($stats->female_last_update ?? null)
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
        $searchId = $request->input('search_id');
        if (!empty($searchId)) {
            $query->where(function($q) use ($searchId) {
                $q->where('academic_id', 'like', '%' . $searchId . '%')
                  ->orWhere('national_id', 'like', '%' . $searchId . '%');
            });
        }
        $searchName = $request->input('search_name');
        if (!empty($searchName)) {
            $query->whereHas('user', function($q) use ($searchName) {
                $q->whereRaw('LOWER(name_en) LIKE ?', ['%' . mb_strtolower($searchName) . '%'])
                  ->orWhereRaw('LOWER(name_ar) LIKE ?', ['%' . mb_strtolower($searchName) . '%']);
            });
        }
        $searchGender = $request->input('search_gender');
        if (!empty($searchGender)) {
            $query->whereHas('user', function($q) use ($searchGender) {
                $q->where('gender', $searchGender);
            });
        }
        $facultyId = $request->input('faculty_id');
        if (!empty($facultyId)) {
            $query->where('faculty_id', $facultyId);
        }
        $searchGovernorateId = $request->input('search_governorate_id');
        if (!empty($searchGovernorateId)) {
            $query->where('governorate_id', $searchGovernorateId);
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
        $singleActions = [
            [
                'action' => 'view',
                'icon' => 'bx bx-show',
                'class' => 'btn-primary',
                'label' => 'View'
            ]
        ];

        return view('components.ui.datatable.table-actions', [
            'mode' => 'single',
            'actions' => [],
            'id' => $student->id,
            'type' => 'Student',
            'singleActions' => $singleActions
        ])->render();
    }

    
} 