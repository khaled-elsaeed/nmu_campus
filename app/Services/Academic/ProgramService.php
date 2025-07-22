<?php

namespace App\Services\Academic;

use App\Models\Academic\Program;
use App\Models\Academic\Faculty;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use App\Exceptions\BusinessValidationException;

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
     * @param Program $program
     * @param array $data
     * @return Program
     */
    public function updateProgram(Program $program, array $data): Program
    {
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
     * @param int $id
     * @return array
     */
    public function getProgram($id)
    {
        $program = Program::select([
            'id', 
            'name_en', 
            'name_ar', 
            'duration_years', 
            'faculty_id'
        ])->find($id);

        if (!$program) {
            throw new BusinessValidationException('Program not found.');
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
     * @param Program $program
     * @return void
     * @throws BusinessValidationException
     */
    public function deleteProgram(Program $program): void
    {
        if ($program->students()->count() > 0) {
            throw new BusinessValidationException('Cannot delete program that has students enrolled.');
        }
        $program->delete();
    }

    /**
     * Get all programs (returns only id and name).
     *
     * @param int|null $facultyId
     * @return array
     */
    public function getAll(?int $facultyId = null): array
    {
        return Program::when(!is_null($facultyId), function ($query) use ($facultyId) {
                $query->where('faculty_id', $facultyId);
            })
            ->get(['id', 'name_en','name_ar'])
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
        $totalPrograms = Program::count();
        $programsWithStudents = Program::has('students')->count();
        $programsWithoutStudents = Program::doesntHave('students')->count();
        $lastUpdateTime = formatDate(Program::max('updated_at'));
        return [
            'total' => [
                'count' => formatNumber($totalPrograms),
                'lastUpdateTime' => $lastUpdateTime
            ],
            'withStudents' => [
                'count' => formatNumber($programsWithStudents),
                'lastUpdateTime' => $lastUpdateTime
            ],
            'withoutStudents' => [
                'count' => formatNumber($programsWithoutStudents),
                'lastUpdateTime' => $lastUpdateTime
            ]
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
            ->addColumn('faculty', fn($program) => $program->faculty->name_en)
            ->addColumn('students', fn($program) => $program->students_count)
            ->addColumn('action', fn($program) => $this->renderActionButtons($program))
            ->orderColumn('name', 'name_en $1')
            ->orderColumn('students', 'students_count $1')
            ->orderColumn('faculty', function ($query, $order) {
                $query->join('faculties', 'programs.faculty_id', '=', 'faculties.id')
                      ->orderBy('faculties.name_en', $order);
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
        $facultyId = $request->input('faculty_id');
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
    protected function renderActionButtons($program): string
    {
        return view('components.ui.datatable.data-table-actions', [
            'mode' => 'dropdown',
            'actions' => ['edit', 'delete'],
            'id' => $program->id,
            'type' => 'Program',
            'singleActions' => []
        ])->render();
    }
} 