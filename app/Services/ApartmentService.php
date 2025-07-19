<?php

namespace App\Services;

use App\Models\Apartment;
use Yajra\DataTables\Facades\DataTables;

class ApartmentService extends BaseService
{
    /**
     * The model associated with the service.
     *
     * @var string
     */
    protected $model = Apartment::class;

    /**
     * Show the details of a specific apartment by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function show($id)
    {
        $apartment = $this->find($id);
        if (!$apartment) {
            return null;
        }

        return [
            'id' => $apartment->id,
            'number' => $apartment->number,
            'building' => optional($apartment->building)->number,
            'building_id' => $apartment->building_id,
            'gender_restriction' => optional($apartment->building)->gender_restriction,
            'active' => $apartment->active,
            'total_rooms' => $apartment->total_rooms,
        ];
    }

    /**
     * Get statistics for apartments, grouped by gender restriction.
     *
     * @return array
     */
    public function stats(): array
    {
        $stats = Apartment::join('buildings', 'apartments.building_id', '=', 'buildings.id')
            ->selectRaw('
                COUNT(apartments.id) as total,
                COUNT(CASE WHEN buildings.gender_restriction = "male" THEN 1 END) as male,
                COUNT(CASE WHEN buildings.gender_restriction = "female" THEN 1 END) as female,
                MAX(apartments.updated_at) as last_update,
                MAX(CASE WHEN buildings.gender_restriction = "male" THEN apartments.updated_at END) as male_last_update,
                MAX(CASE WHEN buildings.gender_restriction = "female" THEN apartments.updated_at END) as female_last_update
            ')
            ->first();

        return [
            'total' => [
                'count' => formatNumber($stats->total),
                'lastUpdateTime' => formatDate($stats->last_update),
            ],
            'male' => [
                'count' => formatNumber($stats->male),
                'lastUpdateTime' => formatDate($stats->male_last_update),
            ],
            'female' => [
                'count' => formatNumber($stats->female),
                'lastUpdateTime' => formatDate($stats->female_last_update),
            ],
        ];
    }

    /**
     * Get datatable data for apartments with optional filters.
     *
     * @param array $params
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function datatable(array $params)
    {
        // Join buildings table to allow ordering and filtering by building fields
        $query = Apartment::join('buildings', 'apartments.building_id', '=', 'buildings.id')
            ->selectRaw('apartments.*, apartments.number as apartment_number, buildings.number as building_number, buildings.gender_restriction as building_gender_restriction');

        // Apply search filters
        $this->applySearchFilters($query, $params);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('number', function ($apartment) {
                return 'Apartment ' . $apartment->apartment_number;
            })
            ->editColumn('building', function ($apartment) {
                return 'Building ' . $apartment->building_number;
            })
            ->editColumn('gender_restriction', function ($apartment) {
                return ucfirst($apartment->building_gender_restriction ?? '-');
            })
            ->editColumn('active', fn($apartment) => $apartment->active ? 'Active' : 'Inactive')
            ->addColumn('actions', function ($apartment) {
                return view('components.ui.datatable.data-table-actions', [
                    'mode' => 'both',
                    'id' => $apartment->id,
                    'type' => 'Apartment',
                    'actions' => ['view', 'delete'],
                    'singleIcon' => $apartment->active ? 'bx-x' : 'bx-check',
                    'singleAction' => $apartment->active ? 'deactivate' : 'activate',
                ])->render();
            })
            ->editColumn('created_at', fn($apartment) => formatDate($apartment->created_at))
            ->rawColumns(['actions'])
            ->orderColumn('apartment_number', function ($query, $order) {
                $query->orderByRaw('CAST(apartment_number AS UNSIGNED) ' . $order);
            })
            ->orderColumn('building_number', function ($query, $order) {
                $query->orderByRaw('CAST(building_number AS UNSIGNED) ' . $order);
            })
            ->make(true);
    }

    /**
     * Apply search filters to the apartment query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $params
     * @return void
     */
    private function applySearchFilters($query, array $params): void
    {
        if (!empty($params['search_apartment_number'])) {
            $query->where('apartments.number', 'like', '%' . $params['search_apartment_number'] . '%');
        }
        if (!empty($params['search_building_id'])) {
            $query->whereHas('building', function ($q) use ($params) {
                $q->where('buildings.id', 'like', '%' . $params['search_building_id'] . '%');
            });
        }
        if (!empty($params['search_gender_restriction'])) {
            $query->where('buildings.gender_restriction', $params['search_gender_restriction']);
        }
    }

    /**
     * Update an apartment by ID with the given data.
     *
     * @param int $id
     * @param array $data
     * @return Apartment|null
     */
    public function update($id, array $data): ?Apartment
    {
        $apartment = Apartment::find($id);
        if (!$apartment) {
            return null;
        }
        $updateData = [
            'number' => $data['number'] ?? $apartment->number,
            'gender_restriction' => $data['gender_restriction'] ?? $apartment->gender_restriction,
            'active' => $data['active'] ?? $apartment->active,
        ];
        $apartment->update($updateData);
        return $apartment->fresh('building');
    }
} 