<?php

namespace App\Services\Resident;

use App\Models\Student;
use Yajra\DataTables\Facades\DataTables;
use App\Services\BaseService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StudentService extends BaseService
{
    protected $model = Student::class;


    /**
     * Get student statistics including counts and last update times using raw SQL.
     *
     * @return array
     */
    public function stats(): array
    {
        $stats = Student::join('users', 'students.user_id', '=', 'users.id')
            ->selectRaw('
                COUNT(students.id) as total,
                COUNT(CASE WHEN users.gender = "male" THEN 1 END) as male,
                COUNT(CASE WHEN users.gender = "female" THEN 1 END) as female,
                MAX(students.updated_at) as last_update,
                MAX(CASE WHEN users.gender = "male" THEN students.updated_at END) as male_last_update,
                MAX(CASE WHEN users.gender = "female" THEN students.updated_at END) as female_last_update
            ')
            ->first();

        return [
            'total' => [
                'count' => formatNumber($stats->total),
                'lastUpdateTime' => formatDate($stats->last_update),
            ],
            'male' => [
                'count' => formatNumber($stats->male),
                'lastUpdateTime' => formatDate($stats->male_last_update),
            ],
            'female' => [
                'count' => formatNumber($stats->female),
                'lastUpdateTime' => formatDate($stats->female_last_update),
            ],
        ];
    }

    /**
     * Create a new student and associated user.
     *
     * @param array $data
     * @return Student
     */
    public function create(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            // Create the user first
            $user = User::create([
                'name_en' => $data['name_en'] ?? null,
                'name_ar' => $data['name_ar'] ?? null,
                'gender' => $data['gender'] ?? null,
                'email' => $data['academic_email'] ?? null,
                'password' => $data['password'] ?? bcrypt('password'), // You may want to handle password properly
            ]);

            // Create the student with the user_id
            $studentData = $data;
            $studentData['user_id'] = $user->id;
            // Remove password from student data if present
            unset($studentData['password']);
            $student = Student::create($studentData);

            return $student;
        });
    }

    public function datatable(array $params)
    {
        $query = Student::with(['program', 'program.faculty']);
        // Optionally: add search filters here
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('faculty', function($student) {
                return $student->program?->faculty?->name ?? '-';
            })
            ->addColumn('actions', fn($student) => 
                view('components.ui.datatable.data-table-actions', [
                    'mode' => 'dropdown',
                    'id' => $student->id,
                    'type' => 'Student',
                    'actions' => ['view', 'edit', 'delete'],
                ])->render()
            )
            ->editColumn('created_at', fn($student) => formatDate($student->created_at))
            ->rawColumns(['actions'])
            ->make(true);
    }

} 