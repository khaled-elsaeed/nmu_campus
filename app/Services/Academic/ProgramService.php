<?php

namespace App\Services\Academic;

use App\Models\Academic\Program;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class ProgramService
{
    /**
     * Create a new program.
     *
     * @param array $data
     * @return Program
     */
    public function createProgram(array $data): Program
    {
        return Program::create([
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'],
            'duration_years' => $data['duration_years'],
            'faculty_id' => $data['faculty_id'],
        ]);
    }

    /**
     * Update an existing program.
     *
     * @param int $programId
     * @param array $data
     * @return Program
     */
    public function updateProgram(int $programId, array $data): Program
    {
        $program = Program::findOrFail($programId);

        if(!$program){
            throw new BusinessValidationException(__(':field not found.', ['field' => __('Program')]));
        }

        $program->update([
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'],
            'duration_years' => $data['duration_years'],
            'faculty_id' => $data['faculty_id'],
        ]);
        return $program->fresh();
    }

    /**
     * Get a single program.
     *
     * @param int $programId
     * @return array
     */
    public function getProgram(int $programId): array
    {
        $program = Program::with('faculty')->select(['id', 'name_en', 'name_ar', 'faculty_id', 'duration_years'])->find($programId);
        if (!$program) {
            throw new BusinessValidationException(__(':field not found.', ['field' => __('Program')]));
        }

        return [
            'id' => $program->id,
            'name_en' => $program->name_en,
            'name_ar' => $program->name_ar,
            'name' => $program->name,
            'duration_years' => $program->duration_years,
            'faculty_id' => $program->faculty_id,
        ];
    }

    /**
     * Delete a program.
     *
     * @param int $programId
     * @return void
     * @throws BusinessValidationException
     */
    public function deleteProgram(int $programId): void
    {
        $program = Program::findOrFail($programId);

        if(!$program){
            throw new BusinessValidationException(__(':field not found.', ['field' => __('Program')]));
        }

        if ($program->students()->count() > 0) {
            throw new BusinessValidationException(__('This program cannot be deleted because it has associated students.'));
        }
        $program->delete();
    }

    /**
     * Get all programs by faculty id.
     *
     * @param int $facultyId
     * @return array
     */
    public function getAll(int $facultyId): array
    {
        return Program::where('faculty_id', $facultyId)
            ->get(['id', 'name_en', 'name_ar'])
            ->map(function ($program) {
                return [
                    'id' => $program->id,
                    'name' => $program->name,
                ];
            })
            ->toArray();
    }

    /**
     * Get program statistics.
     *
     * @return array
     */
    public function getStats(): array
{
        $programs = Program::select('programs.id', 'programs.updated_at')
            ->leftJoin('students', 'students.program_id', '=', 'programs.id')
            ->groupBy('programs.id', 'programs.updated_at')
            ->get();

        $totalPrograms = $programs->count();

        $programsWithStudents = $programs->filter(function ($program) {
            return $program->students_count > 0;
        })->count();

        $programsWithoutStudents = $totalPrograms - $programsWithStudents;

        $lastUpdateAll = $programs->max('updated_at');

        $lastUpdateWithStudents = $programs->filter(function ($program) {
            return $program->students_count > 0;
        })->max('updated_at');

        $lastUpdateWithoutStudents = $programs->filter(function ($program) {
            return $program->students_count === 0;
        })->max('updated_at');

        return [
            'programs' => [
                'count' => formatNumber($totalPrograms),
                'lastUpdateTime' => formatDate($lastUpdateAll),
            ],
            'with-students' => [
                'count' => formatNumber($programsWithStudents),
                'lastUpdateTime' => formatDate($lastUpdateWithStudents),
            ],
            'without-students' => [
                'count' => formatNumber($programsWithoutStudents),
                'lastUpdateTime' => formatDate($lastUpdateWithoutStudents),
            ],
        ];
    }


    /**
     * Get program data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $query = Program::with(['faculty', 'students'])->withCount('students');

        $request = request();
        
        $this->applySearchFilters($query, $request);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('faculty', fn($program) => $program->faculty->name)
            ->addColumn('students', fn($program) => $program->students_count)
            ->addColumn('action', fn($program) => $this->renderActionButtons($program))
            ->orderColumn('name', 'name_en $1')
            ->orderColumn('students', 'students_count $1')
            ->orderColumn('faculty', function ($query, $order) {
                $query->join('faculties', 'programs.faculty_id', '=', 'faculties.id')
                      ->orderBy('faculties.name_' . app()->getLocale(), $order);
            })
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
        // Filter by program name
        $searchName = $request->input('search_name');
        if (!empty($searchName)) {
            $query->where(function($q) use ($searchName) {
                $q->whereRaw('LOWER(programs.name_en) LIKE ?', ['%' . mb_strtolower($searchName) . '%'])
                  ->orWhereRaw('LOWER(programs.name_ar) LIKE ?', ['%' . mb_strtolower($searchName) . '%']);
            });
        }
        // Filter by faculty
        $facultyId = $request->input('search_faculty');
        if (!empty($facultyId)) {
            $query->where('faculty_id', $facultyId);
        }
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param Program $program
     * @return string
     */
    public function renderActionButtons(Program $program): string
    {
        return view('components.ui.datatable.table-actions', [
            'mode' => 'dropdown',
            'actions' => ['edit', 'delete'],
            'id' => $program->id,
            'type' => "Program",
            'singleActions' => []
        ])->render();
    }
}