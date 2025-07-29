<?php

namespace App\Services;

use App\Models\Country;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;

class CountryService
{
    /**
     * Create a new country.
     *
     * @param array $data
     * @return Country
     */
    public function createCountry(array $data): Country
    {
        return Country::create([
            'code' => $data['code'],
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'] ?? null,
            'nationality_en' => $data['nationality_en'] ?? null,
            'nationality_ar' => $data['nationality_ar'] ?? null,
        ]);
    }

    /**
     * Update an existing country.
     *
     * @param int $id
     * @param array $data
     * @return Country
     */
    public function updateCountry(int $id, array $data): Country
    {
        $country = Country::findOrFail($id);
        $country->update([
            'code' => $data['code'],
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'] ?? null,
            'nationality_en' => $data['nationality_en'] ?? null,
            'nationality_ar' => $data['nationality_ar'] ?? null,
        ]);
        return $country->fresh();
    }

    /**
     * Get a single country.
     *
     * @param int $id
     * @return array
     */
    public function getCountry(int $id): array
    {
        $country = Country::select(['id', 'code', 'name_en', 'name_ar', 'nationality_en', 'nationality_ar'])->find($id);
        if (!$country) {
            throw new BusinessValidationException('Country not found.');
        }
        return [
            'id' => $country->id,
            'code' => $country->code,
            'name_en' => $country->name_en,
            'name_ar' => $country->name_ar,
            'nationality_en' => $country->nationality_en,
            'nationality_ar' => $country->nationality_ar,
        ];
    }

    /**
     * Delete a country.
     *
     * @param int $id
     * @return void
     * @throws BusinessValidationException
     */
    public function deleteCountry($id): void
    {
        $country = Country::findOrFail($id);
        
        if ($country->governorates()->count() > 0) {
            throw new BusinessValidationException('Cannot delete country that has governorates assigned.');
        }
        
        $country->delete();
    }

    /**
     * Get all countries.
     *
     * @return array
     */
    public function getAll(): array
    {
        return Country::query()
            ->select(['id', 'code', 'name_en', 'name_ar'])
            ->get()
            ->map(function ($country) {
                return [
                    'id' => $country->id,
                    'name' => $country->name,
                ];
            })
            ->toArray();
    }

    /**
     * Get country statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $total = Country::count();
        $withGovernorates = Country::has('governorates')->count();
        $withoutGovernorates = Country::doesntHave('governorates')->count();
        $totalLastUpdate = Country::max('updated_at');
        $withGovernoratesLastUpdate = Country::has('governorates')->max('updated_at');
        $withoutGovernoratesLastUpdate = Country::doesntHave('governorates')->max('updated_at');

        return [
            'total' => [
                'count' => formatNumber($total),
                'lastUpdateTime' => formatDate($totalLastUpdate)
            ],
            'withGovernorates' => [
                'count' => formatNumber($withGovernorates),
                'lastUpdateTime' => formatDate($withGovernoratesLastUpdate)
            ],
            'withoutGovernorates' => [
                'count' => formatNumber($withoutGovernorates),
                'lastUpdateTime' => formatDate($withoutGovernoratesLastUpdate)
            ]
        ];
    }

    /**
     * Get country data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $countries = Country::withCount(['governorates']);

        $countries = $this->applySearchFilters($countries);

        return DataTables::of($countries)
            ->addIndexColumn()
            ->addColumn('name', function ($country) {
                return $country->name;
            })
            ->addColumn('nationality', function ($country) {
                return $country->nationality;
            })
            ->addColumn('governorates', function ($country) {
                return formatNumber($country->governorates_count);
            })
            ->addColumn('action', function ($country) {
                return $this->renderActionButtons($country);
            })
            ->orderColumn('name', 'name_en $1')
            ->orderColumn('nationality', 'nationality_en $1')
            ->orderColumn('governorates', 'governorates_count $1')
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
     * @param Country $country
     * @return string
     */
    public function renderActionButtons(Country $country): string
    {
        return view('components.ui.datatable.data-table-actions', [
            'mode' => 'dropdown',
            'actions' => ['edit', 'delete'],
            'id' => $country->id,
            'type' => 'Country',
            'singleActions' => []
        ])->render();
    }
}