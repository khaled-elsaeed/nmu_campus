<?php

namespace App\Services\Academic;

use App\Models\Academic\Faculty;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;

class FacultyService
{
    /**
     * Create a new faculty.
     *
     * @param array $data
     * @return Faculty
     */
    public function createFaculty(array $data): Faculty
    {
        return Faculty::create([
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'],
        ]);
    }

    /**
     * Update an existing faculty.
     *
     * @param int $id
     * @param array $data
     * @return Faculty
     */
    public function updateFaculty(int $id, array $data): Faculty
    {
        $faculty = Faculty::findOrFail($id);
        $faculty->update([
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'],
        ]);
        return $faculty->fresh();
    }

    /**
     * Get a single faculty with its programs.
     *
     * @param int $id
     * @return array
     */
    public function getFaculty(int $id): array
    {
        $faculty = Faculty::select(['id', 'name_en', 'name_ar'])->find($id);
        if (!$faculty) {
            throw new BusinessValidationException(__(':field not found.', ['field' => __('faculty')]));
        }
        return [
            'id' => $faculty->id,
            'name_en' => $faculty->name_en,
            'name_ar' => $faculty->name_ar,
        ];
    }

    /**
     * Delete a faculty.
     *
     * @param int $id
     * @return void
     * @throws BusinessValidationException
     */
    public function deleteFaculty($id): void
    {
        $faculty = Faculty::findOrFail($id);
        
        if ($faculty->students()->count() > 0 || $faculty->staff()->count() > 0) {
            throw new BusinessValidationException(__('This faculty cannot be deleted because it has associated students or staff members.'));
        }
        foreach ($faculty->programs as $program) {
            if ($program->students()->count() > 0) {
                throw new BusinessValidationException(__('This program cannot be deleted because it has associated students.'));
            }
        }
        $faculty->delete();
    }

    /**
     * Get all faculties.
     *
     * @return array
     */
    public function getAll(): array
    {
        return Faculty::query()
            ->select(['id', 'name_en', 'name_ar'])
            ->get()
            ->map(function ($faculty) {
                return [
                    'id' => $faculty->id,
                    'name' => $faculty->name,
                ];
            })
            ->toArray();
    }

    /**
     * Get faculty statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $stats = Faculty::selectRaw('
            COUNT(DISTINCT faculties.id) as total,
            COUNT(DISTINCT CASE WHEN programs.id IS NOT NULL THEN faculties.id END) as with_programs,
            COUNT(DISTINCT CASE WHEN programs.id IS NULL THEN faculties.id END) as without_programs,
            MAX(faculties.updated_at) as faculties_last_update,
            MAX(CASE WHEN programs.id IS NOT NULL THEN faculties.updated_at END) as with_programs_last_update,
            MAX(CASE WHEN programs.id IS NULL THEN faculties.updated_at END) as without_programs_last_update
        ')
        ->leftJoin('programs', 'faculties.id', '=', 'programs.faculty_id')
        ->first();

        return [
            'faculties' => [
                'count' => formatNumber($stats->total),
                'lastUpdateTime' => formatDate($stats->faculties_last_update),
            ],
            'with-programs' => [
                'count' => formatNumber($stats->with_programs),
                'lastUpdateTime' => formatDate($stats->with_programs_last_update),
            ],
            'without-programs' => [
                'count' => formatNumber($stats->without_programs),
                'lastUpdateTime' => formatDate($stats->without_programs_last_update),
            ],
        ];
    }


    /**
     * Get faculty data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $faculties = Faculty::withCount(['programs', 'students','staff']);

        $faculties = $this->applySearchFilters($faculties);

        return DataTables::of($faculties)
            ->addIndexColumn()
            ->addColumn('programs_count', function ($faculty) {
                return formatNumber($faculty->programs_count);
            })
            ->addColumn('students_count', function ($faculty) {
                return formatNumber($faculty->students_count);
            })
            ->addColumn('staff_count', function ($faculty) {
                return formatNumber($faculty->staff_count);
            })
            ->editColumn('created_at',function($faculty){
                return formatDate($faculty->created_at);
            })
            ->addColumn('action', function ($faculty) {
                return $this->renderActionButtons($faculty);
            })
            ->orderColumn('name', function ($query, $order) {
                $lang = app()->getLocale();
                $column = $lang === 'ar' ? 'name_ar' : 'name_en';
                $query->orderBy($column, $order);
            })
            ->orderColumn('programs_count', 'programs_count $1')
            ->orderColumn('students_count', 'students_count $1')
            ->orderColumn('staff_count', 'staff_count $1')

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
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name_en) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(name_ar) LIKE ?', ['%' . $search . '%']);
            });
        }
        return $query;
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param Faculty $faculty
     * @return string
     */
    public function renderActionButtons(Faculty $faculty): string
    {
        return view('components.ui.datatable.table-actions', [
            'mode' => 'dropdown',
            'actions' => ['edit', 'delete'],
            'id' => $faculty->id,
            'type' => 'Faculty',
            'singleActions' => []
        ])->render();
    }
}