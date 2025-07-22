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
     * @param Faculty $faculty
     * @param array $data
     * @return Faculty
     */
    public function updateFaculty(Faculty $faculty, array $data): Faculty
    {
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
            throw new BusinessValidationException('Faculty not found.');
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
            throw new BusinessValidationException('Cannot delete faculty that has students assigned or stuff.');
        }
        foreach ($faculty->programs as $program) {
            if ($program->students()->count() > 0) {
                throw new BusinessValidationException('Cannot delete faculty that has programs with students assigned.');
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
        $total = Faculty::count();
        $withPrograms = Faculty::has('programs')->count();
        $withoutPrograms = Faculty::doesntHave('programs')->count();
        $totalLastUpdate = Faculty::max('updated_at');
        $withProgramsLastUpdate = Faculty::has('programs')->max('updated_at');
        $withoutProgramsLastUpdate = Faculty::doesntHave('programs')->max('updated_at');

        return [
            'total' => [
                'count' => formatNumber($total),
                'lastUpdateTime' => formatDate($totalLastUpdate)
            ],
            'withPrograms' => [
                'count' => formatNumber($withPrograms),
                'lastUpdateTime' => formatDate($withProgramsLastUpdate)
            ],
            'withoutPrograms' => [
                'count' => formatNumber($withoutPrograms),
                'lastUpdateTime' => formatDate($withoutProgramsLastUpdate)
            ]
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
            ->addColumn('name', function ($faculty) {
                return $faculty->name;
            })
            ->addColumn('programs', function ($faculty) {
                return formatNumber($faculty->programs_count);
            })
            ->addColumn('students', function ($faculty) {
                return formatNumber($faculty->students_count);
            })
            ->addColumn('staff', function ($faculty) {
                return formatNumber($faculty->staff_count);
            })
            ->addColumn('action', function ($faculty) {
                return $this->renderActionButtons($faculty);
            })
            ->orderColumn('name', 'name_en $1')
            ->orderColumn('programs', 'programs_count $1')
            ->orderColumn('students', 'students_count $1')
            ->orderColumn('staff', 'staff_count $1')

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
        return view('components.ui.datatable.data-table-actions', [
            'mode' => 'dropdown',
            'actions' => ['edit', 'delete'],
            'id' => $faculty->id,
            'type' => 'Faculty',
            'singleActions' => []
        ])->render();
    }
} 