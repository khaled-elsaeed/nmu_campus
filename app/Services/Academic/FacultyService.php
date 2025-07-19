<?php

namespace App\Services\Academic;

use App\Models\Faculty;
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
            'name' => $data['name'],
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
            'name' => $data['name'],
        ]);
        return $faculty->fresh();
    }

    /**
     * Get a single faculty with its programs.
     *
     * @param int $id
     * @return Faculty
     */
    public function getFaculty($id): Faculty
    {
        return Faculty::with('programs')->findOrFail($id);
    }

    /**
     * Delete a faculty.
     *
     * @param Faculty $faculty
     * @return void
     * @throws BusinessValidationException
     */
    public function deleteFaculty(Faculty $faculty): void
    {
        if ($faculty->students()->count() > 0) {
            throw new BusinessValidationException('Cannot delete faculty that has students assigned.');
        }

        foreach ($faculty->programs as $program) {
            if ($program->students()->count() > 0) {
                throw new BusinessValidationException('Cannot delete faculty that has programs with students assigned.');
            }
        }
        $faculty->delete();
    }

    /**
     * Get all faculties (for dropdown and forms).
     *
     * @return array
     */
    public function getAll(): array
    {
        return Faculty::get()->map(function ($faculty) {
            return [
                'id' => $faculty->id,
                'name' => $faculty->name,
            ];
        })->toArray();
    }

    /**
     * Get faculty statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $totalFaculties = Faculty::count();
        $facultiesWithPrograms = Faculty::has('programs')->count();
        $facultiesWithoutPrograms = Faculty::doesntHave('programs')->count();
        $lastUpdateTime = formatDate(Faculty::max('updated_at'));

        return [
            'total' => [
                'total' => formatNumber($totalFaculties),
                'lastUpdateTime' => $lastUpdateTime
            ],
            'withPrograms' => [
                'total' => formatNumber($facultiesWithPrograms),
                'lastUpdateTime' => $lastUpdateTime
            ],
            'withoutPrograms' => [
                'total' => formatNumber($facultiesWithoutPrograms),
                'lastUpdateTime' => $lastUpdateTime
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
        $faculties = Faculty::with(['programs'])->withCount(['programs', 'students']);
        $faculties = $this->applySearchFilters($faculties);

        return DataTables::of($faculties)
            ->addIndexColumn()
            ->addColumn('name', fn($faculty) => $faculty->name)
            ->addColumn('programs', fn($faculty) => $faculty->programs_count)
            ->addColumn('students', fn($faculty) => $faculty->students_count)
            ->addColumn('action', fn($faculty) => $this->renderActionButtons($faculty))
            ->orderColumn('name', 'name_en $1')
            ->orderColumn('programs', 'programs_count $1')
            ->orderColumn('students', 'students_count $1')
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