<?php

namespace App\Services;

use App\Models\Building;
use Yajra\DataTables\Facades\DataTables;
use App\Exceptions\BusinessValidationException;

class BuildingService extends BaseService
{
    /**
     * The model class used by BaseService.
     *
     * @see \App\Services\BaseService
     */
    protected $model = Building::class;


    /**
     * Get building statistics including counts and last update times using raw SQL.
     *
     * @return array
     */
    public function stats(): array
    {
        $stats = Building::selectRaw('
                COUNT(id) as total,
                COUNT(CASE WHEN gender_restriction = "male" THEN 1 END) as male,
                COUNT(CASE WHEN gender_restriction = "female" THEN 1 END) as female,
                MAX(updated_at) as last_update,
                MAX(CASE WHEN gender_restriction = "male" THEN updated_at END) as male_last_update,
                MAX(CASE WHEN gender_restriction = "female" THEN updated_at END) as female_last_update
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
     * DataTable server-side processing for buildings using Yajra DataTables.
     *
     * @param array $params
     * @return mixed
     */
    public function datatable(array $params)
    {
        $query = Building::query();

        $this->applySearchFilters($query, $params);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('number', function($building) {
                return 'Apartment ' . $building->number;
            })
            ->editColumn('active', fn($building) => $building->active ? 'Active' : 'Inactive')
            ->addColumn('actions', fn($building) => 
                view('components.ui.datatable.data-table-actions', [
                    'mode' => 'both',
                    'id' => $building->id,
                    'type' => 'Building',
                    'actions' => ['view', 'edit', 'delete'],
                    'singleIcon' => $building->active ? 'bx-x' : 'bx-check',
                    'singleAction' => $building->active ? 'deactivate' : 'activate',
                    'singleLabel' => $building->active ? 'Deactivate' : 'Activate',
                ])->render()
            )
            ->editColumn('created_at', fn($building) => formatDate($building->created_at))
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Apply search filters to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $params
     * @return void
     */
    private function applySearchFilters($query, array $params): void
    {
        if (!empty($params['search_gender_restriction'])) {
            $query->where('gender_restriction', $params['search_gender_restriction']);
        }

        if (isset($params['search_active']) && $params['search_active'] !== '') {
            $query->where('active', (bool)$params['search_active']);
        }
    }

    /**
     * Create a building with its apartments and rooms automatically.
     *
     * @param array $data
     * @return Building
     * @throws \Exception
     */
    public function create(array $data): Building
    {
        $totalApartments = $data['total_apartments'];
        $roomsPerApartment = $data['rooms_per_apartment'];
        $data['total_rooms'] = $totalApartments * $roomsPerApartment;
        unset($data['rooms_per_apartment']);
        $hasDoubleRooms = $this->hasDoubleRooms($data);
        $data['has_double_rooms'] = $hasDoubleRooms;
        $apartmentsData = $data['apartments'] ?? [];

        $building = Building::create($data);

        $this->createApartmentsAndRooms($building, $totalApartments, $roomsPerApartment, $hasDoubleRooms, $apartmentsData);

        return $building->load('apartments.rooms');
    }

    /**
     * Check if the building has double rooms.
     *
     * @param array $data
     * @return bool
     */
    private function hasDoubleRooms(array $data): bool
    {
        return isset($data['has_double_rooms']) && $data['has_double_rooms'] === 'on';
    }

    /**
     * Create apartments and rooms for a building.
     *
     * @param Building $building
     * @param int $totalApartments
     * @param int $roomsPerApartment
     * @param bool $hasDoubleRooms
     * @param array $apartmentsData
     * @return void
     */
    private function createApartmentsAndRooms(
        Building $building,
        int $totalApartments,
        int $roomsPerApartment,
        bool $hasDoubleRooms,
        array $apartmentsData
    ): void {
        for ($i = 1; $i <= $totalApartments; $i++) {
            $apartment = $building->apartments()->create([
                'number' => (string)$i,
                'total_rooms' => $roomsPerApartment,
                'active' => true,
            ]);

            $doubleRooms = [];
            if ($hasDoubleRooms) {
                $doubleRooms = $apartmentsData[$i - 1]['double_rooms'] ?? [];
            }

            $this->createRoomsForApartment($apartment, $roomsPerApartment, $doubleRooms);
        }
    }

    /**
     * Create rooms for an apartment.
     *
     * @param \App\Models\Apartment $apartment
     * @param int $roomsPerApartment
     * @param array $doubleRooms
     * @return void
     */
    private function createRoomsForApartment($apartment, int $roomsPerApartment, array $doubleRooms): void
    {
        for ($j = 1; $j <= $roomsPerApartment; $j++) {
            $isDouble = in_array($j, $doubleRooms);
            $capacity = $isDouble ? 2 : 1;

            $apartment->rooms()->create([
                'number' => (string)$j,
                'type' => $isDouble ? 'double' : 'single',
                'capacity' => $capacity,
                'current_occupancy' => 0,
                'available_capacity' => $capacity,
                'purpose' => 'housing',
                'occupancy_status' => 'available',
                'active' => true,
            ]);
        }
    }

    /**
     * Update a building by ID.
     *
     * @param int $id
     * @param array $data
     * @return Building|null
     */
    public function update($id, array $data): ?Building
    {
        $building = Building::find($id);
        if (!$building) {
            return null;
        }

        $updateData = $this->prepareUpdateData($building, $data);
        $building->update($updateData);

        return $building->fresh('apartments.rooms');
    }

    /**
     * Prepare update data for a building.
     *
     * @param Building $building
     * @param array $data
     * @return array
     */
    private function prepareUpdateData(Building $building, array $data): array
    {
        return [
            'number' => $data['number'] ?? $building->number,
            'gender_restriction' => $data['gender_restriction'] ?? $building->gender_restriction,
            'active' => $data['active'] ?? $building->active,
        ];
    }

        /**
     * Set the active status of an apartment by ID.
     *
     * @param int $id
     * @param bool $active
     * @return Apartment|null
     */
    public function setActiveStatus($id, bool $active): ?Apartment
    {
        $apartment = Apartment::find($id);
        if (!$apartment) {
            return null;
        }
        $apartment->active = $active;
        $apartment->save();
        return $apartment->fresh('building');
    }
}