<?php

namespace App\Services\Resident;

use App\Models\Staff;
use App\Models\User;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class StaffService
{
    /**
     * The model class to use for this service.
     * @var string
     */
    protected $model = Staff::class;

    /**
     * Create a new staff and associated user.
     *
     * @param array $data
     * @return Staff
     */
    public function createStaff(array $data): Staff
    {
        return DB::transaction(function () use ($data) {
            $user = $this->createUser($data);
            $staff = $this->createStaffProfile($user, $data);
            return $staff->fresh(['user', 'department', 'staffCategory', 'faculty']);
        });
    }

    /**
     * Create a new user for staff.
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
            'email' => $data['email'] ?? null,
            'password' => $data['password'] ?? bcrypt('password'),
        ]);
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
        return Staff::create([
            'user_id' => $user->id,
            'staff_category_id' => $data['staff_category_id'],
            'faculty_id' => $data['faculty_id'],
            'department_id' => $data['department_id'],
            'notes' => $data['notes'] ?? null,
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
            return $staff->fresh(['user', 'department', 'staffCategory', 'faculty']);
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
        if (!empty($data['password'])) {
            $user->update(['password' => bcrypt($data['password'])]);
        }
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
        $staff->update([
            'staff_category_id' => $data['staff_category_id'],
            'faculty_id' => $data['faculty_id'],
            'department_id' => $data['department_id'],
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Get a single staff with relationships.
     *
     * @param int $id
     * @return Staff
     */
    public function getStaff($id): Staff
    {
        return Staff::with(['user', 'department', 'staffCategory', 'faculty'])->findOrFail($id);
    }

    /**
     * Delete a staff and associated user.
     *
     * @param Staff $staff
     * @return void
     * @throws BusinessValidationException
     */
    public function deleteStaff(Staff $staff): void
    {
        $user = $staff->user;
        if ($user) {
            $user->delete();
        }
    }

    /**
     * Get all staff (for dropdowns/forms).
     *
     * @return array
     */
    public function getAll(): array
    {
        return Staff::with(['user', 'department', 'staffCategory', 'faculty'])
            ->get()
            ->map(function ($staff) {
                return [
                    'id' => $staff->id,
                    'name' => $staff->user?->name_en,
                    'faculty' => $staff->faculty?->name,
                    'department' => $staff->department?->name,
                    'category' => $staff->staffCategory?->name,
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
                'total' => formatNumber($totalStaff),
                'lastUpdateTime' => $lastUpdateTime
            ],
            'male' => [
                'total' => formatNumber($maleStaff),
                'lastUpdateTime' => $maleLastUpdate
            ],
            'female' => [
                'total' => formatNumber($femaleStaff),
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
        $query = Staff::with(['user', 'department', 'staffCategory', 'faculty']);
        $query = $this->applySearchFilters($query);
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name', fn($staff) => $staff->user?->name_en)
            ->addColumn('faculty', fn($staff) => $staff->faculty?->name)
            ->addColumn('department', fn($staff) => $staff->department?->name)
            ->addColumn('category', fn($staff) => $staff->staffCategory?->name)
            ->addColumn('gender', fn($staff) => $staff->user?->gender)
            ->addColumn('created_at', fn($staff) => formatDate($staff->created_at))
            ->addColumn('action', fn($staff) => $this->renderActionButtons($staff))
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
        if (request()->filled('faculty_id')) {
            $query->where('faculty_id', request('faculty_id'));
        }
        if (request()->filled('department_id')) {
            $query->where('department_id', request('department_id'));
        }
        if (request()->filled('category_id')) {
            $query->where('staff_category_id', request('category_id'));
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