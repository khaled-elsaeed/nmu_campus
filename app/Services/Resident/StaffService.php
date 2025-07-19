<?php

namespace App\Services\Resident;

use App\Models\Staff;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class StaffService
{
    protected $model = Staff::class;

    /**
     * Get staff statistics including counts and last update times using raw SQL.
     *
     * @return array
     */
    public function stats(): array
    {
        $stats = Staff::join('users', 'staff.user_id', '=', 'users.id')
            ->selectRaw('
                COUNT(staff.id) as total,
                COUNT(CASE WHEN users.gender = "male" THEN 1 END) as male,
                COUNT(CASE WHEN users.gender = "female" THEN 1 END) as female,
                MAX(staff.updated_at) as last_update,
                MAX(CASE WHEN users.gender = "male" THEN staff.updated_at END) as male_last_update,
                MAX(CASE WHEN users.gender = "female" THEN staff.updated_at END) as female_last_update
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
     * Create a new staff and associated user.
     *
     * @param array $data
     * @return Staff
     */
    public function create(array $data): Staff
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name_en' => $data['name_en'] ?? null,
                'name_ar' => $data['name_ar'] ?? null,
                'gender' => $data['gender'] ?? null,
                'email' => $data['email'] ?? null,
                'password' => $data['password'] ?? bcrypt('password'),
            ]);
            $staff = Staff::create([
                'user_id' => $user->id,
                'staff_category_id' => $data['staff_category_id'],
                'faculty_id' => $data['faculty_id'],
                'department_id' => $data['department_id'],
                'notes' => $data['notes'] ?? null,
            ]);
            return $staff;
        });
    }

    public function update($id, array $data): Staff
    {
        return DB::transaction(function () use ($id, $data) {
            $staff = Staff::findOrFail($id);
            $user = $staff->user;
            $user->update([
                'name_en' => $data['name_en'] ?? $user->name_en,
                'name_ar' => $data['name_ar'] ?? $user->name_ar,
                'gender' => $data['gender'] ?? $user->gender,
                'email' => $data['email'] ?? $user->email,
            ]);
            if (!empty($data['password'])) {
                $user->update(['password' => bcrypt($data['password'])]);
            }
            $staff->update([
                'staff_category_id' => $data['staff_category_id'],
                'faculty_id' => $data['faculty_id'],
                'department_id' => $data['department_id'],
                'notes' => $data['notes'] ?? null,
            ]);
            return $staff;
        });
    }

    public function delete($id): bool
    {
        $staff = Staff::find($id);
        if (!$staff) return false;
        $staff->delete();
        return true;
    }

    public function find($id): ?Staff
    {
        return Staff::with(['department', 'staffCategory', 'faculty', 'user'])->find($id);
    }

    public function datatable(array $params)
    {
        $query = Staff::with(['department', 'staffCategory', 'faculty', 'user']);
        // Optionally: add search filters here
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('department', function($staff) {
                return $staff->department?->name ?? '-';
            })
            ->addColumn('category', function($staff) {
                return $staff->staffCategory?->name ?? '-';
            })
            ->addColumn('faculty', function($staff) {
                return $staff->faculty?->name ?? '-';
            })
            ->addColumn('name', function($staff) {
                return $staff->user?->name_en ?? '-';
            })
            ->addColumn('gender', function($staff) {
                return $staff->user?->gender ?? '-';
            })
            ->addColumn('actions', fn($staff) => 
                view('components.ui.datatable.data-table-actions', [
                    'mode' => 'dropdown',
                    'id' => $staff->id,
                    'type' => 'Staff',
                    'actions' => ['view', 'edit', 'delete'],
                ])->render()
            )
            ->editColumn('created_at', fn($staff) => formatDate($staff->created_at))
            ->rawColumns(['actions'])
            ->make(true);
    }
} 