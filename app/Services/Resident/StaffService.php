<?php

namespace App\Services\Resident;

use App\Models\Resident\Staff;
use App\Models\User;
use App\Models\Academic\Faculty;
use App\Models\{Department,CampusUnit,StaffCategory};
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

            return $staff->fresh(['user', 'unit', 'staffCategory']);
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
        $unitData = $this->determineUnitData($data);

        if ($unitData['unit_type'] && !$unitData['unit_id']) {
            throw new BusinessValidationException('Unit must be selected for this staff category.');
        }

        return Staff::create([
            'user_id' => $user->id,
            'staff_category_id' => $data['staff_category_id'],
            'unit_type' => $unitData['unit_type'],
            'unit_id' => $unitData['unit_id'],
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
            return $staff->fresh(['user', 'unit', 'staffCategory']);
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
        $unitData = $this->determineUnitData($data);
        
        $staff->update([
            'staff_category_id' => $data['staff_category_id'],
            'unit_type' => $unitData['unit_type'],
            'unit_id' => $unitData['unit_id'],
            'notes' => $data['notes'] ?? null,
            'national_id' => $data['national_id'] ?? $staff->national_id,
        ]);
    }

    /**
     * Determine unit type and ID based on staff category type.
     *
     * @param array $data
     * @return array
     */
    private function determineUnitData(array $data): array
    {
        if (!isset($data['staff_category_id']) || !$data['staff_category_id']) {
            return [
                'unit_type' => null,
                'unit_id' => null
            ];
        }

        // Get the staff category to determine the type
        $staffCategory = StaffCategory::find($data['staff_category_id']);
        
        if (!$staffCategory) {
            return [
                'unit_type' => null,
                'unit_id' => null
            ];
        }

        // Get unit_id from the form data
        $unitId = isset($data['unit_id']) ? (int) $data['unit_id'] : null;

        // Determine unit type based on staff category type
        switch ($staffCategory->type) {
            case 'faculty':
                return [
                    'unit_type' => Faculty::class,
                    'unit_id' => $unitId
                ];
                
            case 'administrative':
                return [
                    'unit_type' => Department::class,
                    'unit_id' => $unitId
                ];
                
            case 'campus':
                return [
                    'unit_type' => CampusUnit::class,
                    'unit_id' => $unitId
                ];
                
            default:
                return [
                    'unit_type' => null,
                    'unit_id' => null
                ];
        }
    }

    /**
     * Get a single staff with relationships.
     *
     * @param int $id
     * @return array
     */
    public function getStaff(int $id): array
    {
        $staff = Staff::with(['user', 'staffCategory', 'faculty', 'department', 'campusUnit'])->find($id);
    
        if (!$staff) {
            throw new BusinessValidationException('Staff not found.');
        }
    
        return [
            'id' => $staff->id,
            'user_id' => $staff->user_id,
            'staff_category_id' => $staff->staffCategory->id,
            'staff_category_type' => $staff->staffCategory->type ?? null,
            'name_en' => $staff->user->name_en ?? null,
            'name_ar' => $staff->user->name_ar ?? null,
            'name' => $staff->user->name,
            'email' => $staff->user->email ?? null,
            'national_id' => $staff->national_id ?? null,
            'gender' => $staff->user->gender ?? null,
            'unit' => $staff->work_unit, 
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
        $totalStaff = Staff::count();
        $maleStaff = Staff::whereHas('user', fn($q) => $q->where('gender', 'male'))->count();
        $femaleStaff = Staff::whereHas('user', fn($q) => $q->where('gender', 'female'))->count();
        $lastUpdateTime = formatDate(Staff::max('updated_at'));
        $maleLastUpdate = formatDate(Staff::whereHas('user', fn($q) => $q->where('gender', 'male'))->max('updated_at'));
        $femaleLastUpdate = formatDate(Staff::whereHas('user', fn($q) => $q->where('gender', 'female'))->max('updated_at'));
        return [
            'total' => [
                'count' => formatNumber($totalStaff),
                'lastUpdateTime' => $lastUpdateTime
            ],
            'male' => [
                'count' => formatNumber($maleStaff),
                'lastUpdateTime' => $maleLastUpdate
            ],
            'female' => [
                'count' => formatNumber($femaleStaff),
                'lastUpdateTime' => $femaleLastUpdate
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
        $query = Staff::with(['user', 'unit', 'staffCategory']);
        $query = $this->applySearchFilters($query);
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name', fn($staff) => $staff->user?->name_en)
            ->editColumn('unit_name', fn($staff) => ucfirst($staff->work_unit['name']))
            ->editColumn('unit_type', fn($staff) => $staff->work_unit['type'] ?? null)
            ->addColumn('category', fn($staff) => $staff->staffCategory?->name)
            ->addColumn('gender', fn($staff) => $staff->user?->gender)
            ->addColumn('created_at', fn($staff) => formatDate($staff->created_at))
            ->addColumn('action', fn($staff) => $this->renderActionButtons($staff))
            
            // Order columns for related tables
            ->orderColumn('name', function ($query, $order) {
                return $query->leftJoin('users', 'staff.user_id', '=', 'users.id')
                             ->orderBy('users.name_en', $order);
            })
            ->orderColumn('unit_name', function ($query, $order) {
                return $query->leftJoin('faculties', function($join) {
                    $join->on('staff.unit_id', '=', 'faculties.id')
                         ->where('staff.unit_type', '=', Faculty::class);
                })
                ->leftJoin('departments', function($join) {
                    $join->on('staff.unit_id', '=', 'departments.id')
                         ->where('staff.unit_type', '=', Department::class);
                })
                ->leftJoin('campus_units', function($join) {
                    $join->on('staff.unit_id', '=', 'campus_units.id')
                         ->where('staff.unit_type', '=', CampusUnit::class);
                })
                ->orderByRaw("COALESCE(faculties.name_en, departments.name_en, campus_units.name_en) {$order}");
            })
            ->orderColumn('unit_type', 'staff.unit_type $1')
            ->orderColumn('category', function ($query, $order) {
                return $query->leftJoin('staff_categories', 'staff.staff_category_id', '=', 'staff_categories.id')
                             ->orderBy('staff_categories.name_en', $order);
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
            $query->where('unit_type', Faculty::class)
                  ->where('unit_id', request('search_faculty_id'));
        }
        if (request()->filled('search_department_id')) {
            $query->where('unit_type', Department::class)
                  ->where('unit_id', request('search_department_id'));
        }
        if (request()->filled('search_campus_unit_id')) {
            $query->where('unit_type', CampusUnit::class)
                  ->where('unit_id', request('search_campus_unit_id'));
        }
        if (request()->filled('search_category_id')) {
            $query->where('staff_category_id', request('search_category_id'));
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
        return view('components.ui.datatable.data-table-actions', [
            'mode' => 'dropdown',
            'actions' => ['view', 'edit', 'delete'],
            'id' => $staff->id,
            'type' => 'Staff',
            'singleActions' => []
        ])->render();
    }
} 