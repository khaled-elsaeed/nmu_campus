<?php

namespace App\Services;

use App\Models\City;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;

class CityService
{
    /**
     * Create a new city.
     *
     * @param array $data
     * @return City
     */
    public function createCity(array $data): City
    {
        return City::create([
            'code' => $data['code'],
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'] ?? null,
            'governorate_id' => $data['governorate_id'],
        ]);
    }

    /**
     * Update an existing city.
     *
     * @param int $id
     * @param array $data
     * @return City
     */
    public function updateCity(int $id, array $data): City
    {
        $city = City::findOrFail($id);
        $city->update([
            'code' => $data['code'],
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'] ?? null,
            'governorate_id' => $data['governorate_id'],
        ]);
        return $city->fresh();
    }

    /**
     * Get a single city.
     *
     * @param int $id
     * @return array
     */
    public function getCity(int $id): array
    {
        $city = City::with('governorate')->select(['id', 'code', 'name_en', 'name_ar', 'governorate_id'])->find($id);
        if (!$city) {
            throw new BusinessValidationException('City not found.');
        }
        return [
            'id' => $city->id,
            'code' => $city->code,
            'name_en' => $city->name_en,
            'name_ar' => $city->name_ar,
            'governorate_id' => $city->governorate_id,
            'governorate' => $city->governorate ? [
                'id' => $city->governorate->id,
                'name' => $city->governorate->name,
            ] : null,
        ];
    }

    /**
     * Delete a city.
     *
     * @param int $id
     * @return void
     * @throws BusinessValidationException
     */
    public function deleteCity($id): void
    {
        $city = City::findOrFail($id);
        
        if ($city->students()->count() > 0) {
            throw new BusinessValidationException('Cannot delete city that has students assigned.');
        }
        
        $city->delete();
    }

    /**
     * Get all cities for a given governorate.
     *
     * @param int|null $governorateId
     * @return array
     * @throws BusinessValidationException
     */
    public function getAll(?int $governorateId = null): array
    {
        if (!$governorateId) {
            throw new BusinessValidationException('Governorate is required to fetch cities.');
        }

        $query = City::query()
            ->select(['id', 'name_en', 'name_ar'])
            ->where('governorate_id', $governorateId);

        return $query->get()
            ->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                ];
            })
            ->toArray();
    }


    /**
     * Get city statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $total = City::count();
        $withStudents = City::has('students')->count();
        $withoutStudents = City::doesntHave('students')->count();
        $totalLastUpdate = City::max('updated_at');
        $withStudentsLastUpdate = City::has('students')->max('updated_at');
        $withoutStudentsLastUpdate = City::doesntHave('students')->max('updated_at');

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
     * Get city data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $cities = City::with(['governorate'])->withCount(['students']);

        $cities = $this->applySearchFilters($cities);

        return DataTables::of($cities)
            ->addIndexColumn()
            ->addColumn('name', function ($city) {
                return $city->name;
            })
            ->addColumn('governorate', function ($city) {
                return $city->governorate ? $city->governorate->name : '-';
            })
            ->addColumn('students', function ($city) {
                return formatNumber($city->students_count);
            })
            ->addColumn('action', function ($city) {
                return $this->renderActionButtons($city);
            })
            ->orderColumn('name', 'name_en $1')
            ->orderColumn('governorate', 'governorate_id $1')
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

        if (request()->filled('governorate_id') && !empty(request('governorate_id'))) {
            $query->where('governorate_id', request('governorate_id'));
        }

        return $query;
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param City $city
     * @return string
     */
    public function renderActionButtons(City $city): string
    {
        return view('components.ui.datatable.table-actions', [
            'mode' => 'dropdown',
            'actions' => ['edit', 'delete'],
            'id' => $city->id,
            'type' => 'City',
            'singleActions' => []
        ])->render();
    }
}