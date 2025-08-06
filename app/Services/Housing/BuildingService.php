<?php

namespace App\Services\Housing;

use App\Models\Housing\Building;
use App\Models\Housing\Apartment;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BuildingService
{

    /**
     * Create a new building with apartments and rooms.
     *
     * @param array $data
     * @return Building
     */
    public function createBuilding(array $data): Building
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
     * Update an existing building.
     *
     * @param Building $building
     * @param array $data
     * @return Building
     */
    public function updateBuilding(Building $building, array $data): Building
    {
        $updateData = $this->prepareUpdateData($building, $data);
        $building->update($updateData);
        return $building->fresh('apartments.rooms');
    }

    /**
     * Get a single building with its apartments and rooms.
     *
     * @param int $id
     * @return array
     */
    public function getBuilding(int $id): array
    {
        $building = Building::select(['id', 'number', 'gender_restriction'])->find($id);

        if (!$building) {
            throw new BusinessValidationException('Building not found.');
        }

        return [
            'id' => $building->id,
            'number' => $building->number,
            'gender' => $building->gender_restriction,
        ];
    }

    /**
     * Delete a building.
     *
     * @param int $id
     * @return void
     * @throws BusinessValidationException
     */
    public function deleteBuilding($id): void
    {
        $building = Building::findOrFail($id);
        foreach ($building->apartments as $apartment) {
            $room = $apartment->rooms()->where('current_occupancy', '>', 1)->first();
            if ($room) {
                throw new BusinessValidationException(
                    "Cannot delete building: Apartment #{$apartment->number} has room #{$room->number} with active reservation."
                );
            }
        }
        $building->delete();
    }

    /**
     * Get all buildings.
     *
     * @return array
     */
    public function getAll(): array
    {
        return Building::select(['id', 'number'])->get()->map(function ($building) {
            return [
                'id' => $building->id,
                'number' => $building->number,
            ];
        })->toArray();
    }

    /**
     * Get building statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $total = Building::count();
        $male = Building::where('gender_restriction', 'male')->count();
        $female = Building::where('gender_restriction', 'female')->count();
        $lastUpdate = Building::max('updated_at');
        $maleLastUpdate = Building::where('gender_restriction', 'male')->max('updated_at');
        $femaleLastUpdate = Building::where('gender_restriction', 'female')->max('updated_at');
        return [
            'buildings' => [
                'count' => formatNumber($total),
                'lastUpdateTime' => formatDate($lastUpdate),
            ],
            'buildings-male' => [
                'count' => formatNumber($male),
                'lastUpdateTime' => formatDate($maleLastUpdate),
            ],
            'buildings-female' => [
                'count' => formatNumber($female),
                'lastUpdateTime' => formatDate($femaleLastUpdate),
            ],
        ];
    }

    /**
     * Get building data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $query = Building::query();

        $query = $this->applySearchFilters($query);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('number', fn($building) => 'Building ' . $building->number)
            ->editColumn('gender_restriction', fn($building) => ucfirst($building->gender_restriction))
            ->editColumn('active', fn($building) => $building->active ? 'Active' : 'Inactive')
            ->editColumn('has_double_rooms', fn($building) => $building->has_double_rooms ? 'Yes' : 'No')
            ->addColumn('current_occupancy', fn($building) => $building->current_occupancy)
            ->addColumn('action', fn($building) => $this->renderActionButtons($building))
            ->orderColumn('number', 'number $1')
            ->orderColumn('active', 'active $1')
            ->orderColumn('has_double_rooms', 'has_double_rooms $1')
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
        $searchGender = request('search_gender_restriction');
        if (!empty($searchGender)) {
            $query->where('gender_restriction', $searchGender);
        }

        $searchActive = request('search_active');
        if (!empty($searchActive)) {
            $query->where('active', $searchActive);
        }

        return $query;
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param Building $building
     * @return string
     */
    public function renderActionButtons(Building $building): string
    {
        $actions = ['edit', 'delete'];

        $singleActions = [];

        $singleActions[] = [
            'action' => $building->active ? 'deactivate' : 'activate',
            'icon' => $building->active ? 'bx bx-toggle-left' : 'bx bx-toggle-right',
            'class' => $building->active ? 'btn-warning' : 'btn-success',
            'label' => $building->active ? 'Deactivate' : 'Activate'
        ];
        

        return view('components.ui.datatable.data-table-actions', [
            'mode' => 'both',
            'actions' => $actions,
            'id' => $building->id,
            'type' => 'Building',
            'singleActions' => $singleActions
        ])->render();
    }

    /**
     * Set a building as active or inactive
     * @param int $id
     * @param bool $active
     * @return Building
     */
    public function setActive($id, bool $active): Building
    {
        $building = Building::findOrFail($id);
        DB::transaction(function () use ($building, $active) {
            $this->handleApartmentsActivation($building, $active);
            $building->active = $active;
            $building->save();
        });
        return $building->fresh();
    }

    /**
     * Activate or deactivate all apartments of a building.
     *
     * @param Building $building
     * @param bool $active
     * @return void
     */
    private function handleApartmentsActivation($building, bool $active)
    {
        foreach ($building->apartments as $apartment) {
            $apartment->active = $active;
            $apartment->save();
            $this->handleRoomsActivation($apartment, $active);
        }
    }

    private function handleRoomsActivation($apartment, bool $active)
    {
        foreach ($apartment->rooms as $room) {
            if ($room->current_occupancy > 0) {
                throw new BusinessValidationException("Cannot change activation status for room {$room->number} in apartment {$apartment->number} because it is currently occupied.");
            }
            $room->active = $active;
            $room->save();
        }
    }

    /**
     * Check if the building has double rooms.
     *
     * @param array $data
     * @return bool
     */
    private function hasDoubleRooms(array $data): bool
    {
        return isset($data['has_double_rooms']) && ($data['has_double_rooms'] === 'on' || $data['has_double_rooms'] === true);
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
     * @param Apartment $apartment
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
        ];
    }
}