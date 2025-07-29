<?php

namespace App\Services;

use App\Models\Nationality;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;

class NationalityService
{
    /**
     * Create a new nationality.
     *
     * @param array $data
     * @return Nationality
     */
    public function createNationality(array $data): Nationality
    {
        return Nationality::create([
            'code' => $data['code'],
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'] ?? null,
        ]);
    }

    /**
     * Update an existing nationality.
     *
     * @param int $id
     * @param array $data
     * @return Nationality
     */
    public function updateNationality(int $id, array $data): Nationality
    {
        $nationality = Nationality::findOrFail($id);
        $nationality->update([
            'code' => $data['code'],
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'] ?? null,
        ]);
        return $nationality->fresh();
    }

    /**
     * Get a single nationality.
     *
     * @param int $id
     * @return array
     */
    public function getNationality(int $id): array
    {
        $nationality = Nationality::select(['id', 'code', 'name_en', 'name_ar'])->find($id);
        if (!$nationality) {
            throw new BusinessValidationException('Nationality not found.');
        }
        return [
            'id' => $nationality->id,
            'code' => $nationality->code,
            'name_en' => $nationality->name_en,
            'name_ar' => $nationality->name_ar,
        ];
    }

    /**
     * Delete a nationality.
     *
     * @param int $id
     * @return void
     * @throws BusinessValidationException
     */
    public function deleteNationality($id): void
    {
        $nationality = Nationality::findOrFail($id);
        
        if ($nationality->students()->count() > 0) {
            throw new BusinessValidationException('Cannot delete nationality that has students assigned.');
        }
        
        $nationality->delete();
    }

    /**
     * Get all nationalities.
     *
     * @return array
     */
    public function getAll(): array
    {
        return Nationality::query()
            ->select(['id', 'code', 'name_en', 'name_ar'])
            ->get()
            ->map(function ($nationality) {
                return [
                    'id' => $nationality->id,
                    'name' => $nationality->name,
                ];
            })
            ->toArray();
    }

    /**
     * Get nationality statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $total = Nationality::count();
        $withStudents = Nationality::has('students')->count();
        $withoutStudents = Nationality::doesntHave('students')->count();
        $totalLastUpdate = Nationality::max('updated_at');
        $withStudentsLastUpdate = Nationality::has('students')->max('updated_at');
        $withoutStudentsLastUpdate = Nationality::doesntHave('students')->max('updated_at');

        return [
            'total' => [
                'count' => formatNumber($total),
                'lastUpdateTime' => formatDate($totalLastUpdate)
            ],
            'withStudents' => [
                'count' => formatNumber($withStudents),
                'lastUpdateTime' => formatDate($withStudentsLastUpdate)
            ],
            'withoutStudents' => [
                'count' => formatNumber($withoutStudents),
                'lastUpdateTime' => formatDate($withoutStudentsLastUpdate)
            ]
        ];
    }

    /**
     * Get nationality data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $nationalities = Nationality::withCount(['students']);

        $nationalities = $this->applySearchFilters($nationalities);

        return DataTables::of($nationalities)
            ->addIndexColumn()
            ->addColumn('name', function ($nationality) {
                return $nationality->name;
            })
            ->addColumn('students', function ($nationality) {
                return formatNumber($nationality->students_count);
            })
            ->addColumn('action', function ($nationality) {
                return $this->renderActionButtons($nationality);
            })
            ->orderColumn('name', 'name_en $1')
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
                  ->orWhereRaw('LOWER(name_ar) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(code) LIKE ?', ['%' . $search . '%']);
            });
        }
        return $query;
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param Nationality $nationality
     * @return string
     */
    public function renderActionButtons(Nationality $nationality): string
    {
        return view('components.ui.datatable.data-table-actions', [
            'mode' => 'dropdown',
            'actions' => ['edit', 'delete'],
            'id' => $nationality->id,
            'type' => 'Nationality',
            'singleActions' => []
        ])->render();
    }
}