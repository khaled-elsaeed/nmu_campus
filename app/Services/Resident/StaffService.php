<?php

namespace App\Services\Resident;

use App\Models\Resident\Staff;
use App\Models\User;
use App\Models\Academic\Faculty;
use App\Models\{Department,CampusUnit};
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\AccountCreated;

class StaffService
{

    /**
     * Create a new staff and associated user.
     *
     * @param array $data
     * @return Staff
     */
    public function createStaff(array $data): Staff
    {
        return DB::transaction(function () use ($data) {
            $passwordData = $this->generatePassword();
            $user = $this->createUser($data, $passwordData['hashed']);
            $staff = $this->createStaffProfile($user, $data);

            $user->notify((new AccountCreated($staff, $passwordData['plain']))->afterCommit());

            return $staff->fresh(['user']);
        });
    }

    /**
     * Create a new user for staff.
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
            'email' => $data['email'] ?? null,
            'password' => $hashedPassword,
        ]);
    }

    /**
     * Generate a password for the staff.
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
     * Create a staff profile for the user.
     *
     * @param User $user
     * @param array $data
     * @return Staff
     */
    private function createStaffProfile(User $user, array $data): Staff
    {
        $facultyId = null;
        $departmentId = null;
        $campusUnitId = null;

        switch ($data['unit_type']) {
            case 'faculty':
                $facultyId = $data['unit_id'] ?? null;
                break;
            case 'administrative':
                $departmentId = $data['unit_id'] ?? null;
                break;
            case 'campus':
                $campusUnitId = $data['unit_id'] ?? null;
                break;
        }

        return Staff::create([
            'user_id' => $user->id,
            'unit_type' => $data['unit_type'],
            'faculty_id' => $facultyId,
            'department_id' => $departmentId,
            'campus_unit_id' => $campusUnitId,
            'notes' => $data['notes'] ?? null,
            'national_id' => $data['national_id'] ?? null,
        ]);
    }

    /**
     * Update an existing staff and associated user.
     *
     * @param Staff $staff
     * @param array $data
     * @return Staff
     */
    public function updateStaff(Staff $staff, array $data): Staff
    {
        return DB::transaction(function () use ($staff, $data) {
            $this->updateUser($staff->user, $data);
            $this->updateStaffProfile($staff, $data);
            return $staff->fresh(['user']);
        });
    }

    /**
     * Update the user associated with the staff.
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
            'email' => $data['email'] ?? $user->email,
        ]);
    }

    /**
     * Update the staff profile.
     *
     * @param Staff $staff
     * @param array $data
     * @return void
     */
    private function updateStaffProfile(Staff $staff, array $data): void
    {
        $facultyId = null;
        $departmentId = null;
        $campusUnitId = null;

        switch ($data['unit_type']) {
            case 'faculty':
                $facultyId = $data['unit_id'] ?? null;
                break;
            case 'administrative':
                $departmentId = $data['unit_id'] ?? null;
                break;
            case 'campus':
                $campusUnitId = $data['unit_id'] ?? null;
                break;
        }

        $staff->update([
            'unit_type' => $data['unit_type'],
            'faculty_id' => $facultyId,
            'department_id' => $departmentId,
            'campus_unit_id' => $campusUnitId,
            'notes' => $data['notes'] ?? null,
            'national_id' => $data['national_id'] ?? $staff->national_id,
        ]);
    }

    /**
     * Get a single staff with relationships.
     *
     * @param int $id
     * @return array
     */
    public function getStaff(int $id): array
    {
        $staff = Staff::with(['user', 'faculty', 'department', 'campusUnit'])->find($id);
    
        if (!$staff) {
            throw new BusinessValidationException('Staff not found.');
        }
    
        return [
            'id' => $staff->id,
            'user_id' => $staff->user_id,
            'unit_type' => $staff->unit_type,
            'name_en' => $staff->user->name_en ?? null,
            'name_ar' => $staff->user->name_ar ?? null,
            'name' => $staff->user->name,
            'email' => $staff->user->email ?? null,
            'national_id' => $staff->national_id ?? null,
            'gender' => $staff->user->gender ?? null,
            'faculty_id' => $staff?->faculty_id,
            'department_id' => $staff?->department_id,
            'campus_unit_id' => $staff?->campus_unit_id,
            'notes' => $staff->notes ?? null,
            'created_at' => $staff->created_at,
        ];
    }
    

    /**
     * Delete a staff and associated user.
     *
     * @param int $staffId
     * @return bool
     * @throws BusinessValidationException
     */
    public function deleteStaff(int $staffId): bool
    {
        $staff = Staff::with('user')->findOrFail($staffId);

        $user = $staff->user;

        $deleted = $user->delete();

        return $deleted;
    }

    /**
     * Get all staff (for dropdowns/forms).
     *
     * @return array
     */
    public function getAll(): array
    {
        return Staff::with('user')->get()->map(function ($staff) {
            return [
                'id' => $staff->id,
                'name' => $staff->user->name,
            ];
        })->toArray();
    }

    /**
     * Get staff statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $stats = Staff::join('users', 'staff.user_id', '=', 'users.id')
            ->selectRaw('
                COUNT(staff.id) as total,
                SUM(CASE WHEN users.gender = "male" THEN 1 ELSE 0 END) as male,
                SUM(CASE WHEN users.gender = "female" THEN 1 ELSE 0 END) as female,
                MAX(staff.updated_at) as last_update,
                MAX(CASE WHEN users.gender = "male" THEN staff.updated_at ELSE NULL END) as male_last_update,
                MAX(CASE WHEN users.gender = "female" THEN staff.updated_at ELSE NULL END) as female_last_update
            ')
            ->first();

        return [
            'staff' => [
                'count' => formatNumber($stats->total ?? 0),
                'lastUpdateTime' => formatDate($stats->last_update ?? null)
            ],
            'staff-male' => [
                'count' => formatNumber($stats->male ?? 0),
                'lastUpdateTime' => formatDate($stats->male_last_update ?? null)
            ],
            'staff-female' => [
                'count' => formatNumber($stats->female ?? 0),
                'lastUpdateTime' => formatDate($stats->female_last_update ?? null)
            ],
        ];
    }

    /**
     * Get staff data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $query = Staff::with(['user']);
        $query = $this->applySearchFilters($query);
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name', fn($staff) => $staff->user?->name_en)
            ->addColumn('gender', fn($staff) => $staff->user?->gender)
            ->addColumn('unit_type', fn($staff) => $staff->unit_type)
            ->addColumn('unit_name', fn($staff) => $staff->unit_name)
            ->addColumn('created_at', fn($staff) => formatDate($staff->created_at))
            ->addColumn('action', fn($staff) => $this->renderActionButtons($staff))
            
            // Order columns for related tables
            ->orderColumn('name', function ($query, $order) {
                return $query->leftJoin('users', 'staff.user_id', '=', 'users.id')
                             ->orderBy('users.name_en', $order);
            })
            ->orderColumn('unit_name', function ($query, $order) {
                return $query->orderBy('staff.unit_name', $order);
            })
            ->orderColumn('unit_type', function ($query, $order) {
                return $query->where('staff.unit_type', $order);
            })
            ->orderColumn('gender', function ($query, $order) {
                return $query->leftJoin('users as users_gender', 'staff.user_id', '=', 'users_gender.id')
                             ->orderBy('users_gender.gender', $order);
            })
            ->orderColumn('created_at', 'staff.created_at $1')
            
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Apply search filters to the query.
     *
     * @param Builder $query
     * @return Builder
     */
    protected function applySearchFilters($query): Builder
    {
        if (request()->filled('search_name') && !empty(request('search_name'))) {
            $search = mb_strtolower(request('search_name'));
            $query->whereHas('user', function ($q) use ($search) {
                $q->whereRaw('LOWER(name_en) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(name_ar) LIKE ?', ['%' . $search . '%']);
            });
        }
        if (request()->filled('search_gender')) {
            $query->whereHas('user', function ($q) {
                $q->where('gender', request('search_gender'));
            });
        }
        if (request()->filled('search_faculty_id')) {
            $query->where('faculty_id', request('search_faculty_id'));
        }
        if (request()->filled('search_department_id')) {
            $query->where('department_id', request('search_department_id'));
        }
        if (request()->filled('search_campus_unit_id')) {
            $query->where('campus_unit_id', request('search_campus_unit_id'));
        }
        if (request()->filled('search_unit_type')) {
            $query->where('unit_type', request('search_unit_type'));
        }
        return $query;
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param Staff $staff
     * @return string
     */
    protected function renderActionButtons($staff): string
    {
        return view('components.ui.datatable.table-actions', [
            'mode' => 'dropdown',
            'actions' => ['view', 'edit', 'delete'],
            'id' => $staff->id,
            'type' => 'Staff',
            'singleActions' => []
        ])->render();
    }
}