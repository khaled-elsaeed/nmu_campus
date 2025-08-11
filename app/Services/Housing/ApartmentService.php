<?php

namespace App\Services\Housing;

use App\Models\Housing\Apartment;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ApartmentService
{
    /**
     * Create a new apartment with rooms.
     *
     * @param array $data
     * @return Apartment
     */
    public function createApartment(array $data): Apartment
    {
        $totalRooms = $data['total_rooms'] ?? 0;
        $apartment = Apartment::create($data);
        $this->createRoomsForApartment($apartment, $totalRooms);
        return $apartment->load('rooms');
    }

    /**
     * Update an existing apartment.
     *
     * @param Apartment $apartment
     * @param array $data
     * @return Apartment
     */
    public function updateApartment(Apartment $apartment, array $data): Apartment
    {
        $apartment->update($this->prepareUpdateData($apartment, $data));
        return $apartment->fresh('rooms');
    }

    /**
     * Get a single apartment with its rooms.
     *
     * @param int $id
     * @return array
     */
    public function getApartment(int $id): array
    {
        $apartment = Apartment::with('building')->find($id);
        if (!$apartment) {
            throw new BusinessValidationException(__(':field not found.', ['field' => __('building')]));
        }

        return [
            'id' => $apartment->id,
            'name' => $apartment->formattedName,
            'building' => $apartment->building?->formattedName,
            'currentOccupancy' => $apartment->currentOccupancy,
            'gender' => $apartment->formattedGender,
            'roomsCount' => formatNumber($apartment->total_rooms),
            'active' => ($apartment->active ? __('active') : __('inactive')),
        ];
    }

    /**
     * Delete an apartment.
     *
     * @param int $id
     * @return void
     * @throws BusinessValidationException
     */
    public function deleteApartment($id): void
    {
        $apartment = Apartment::findOrFail($id);

        if ($apartment->rooms()->sum('current_occupancy') > 0) {
            throw new BusinessValidationException(__('This apartment cannot be deleted because it has residents.'));
        }
        $apartment->delete();
    }

    /**
     * Get all apartments for a specific building.
     *
     * @param int $buildingId
     * @return array
     */
    public function getAll(int $buildingId): array
    {
        return Apartment::where('building_id', $buildingId)
            ->select(['id', 'number'])
            ->get()
            ->map(function ($apartment) {
                return [
                    'id' => $apartment->id,
                    'number' => $apartment->number,
                ];
            })
            ->toArray();
    }

    /**
     * Get apartment statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $stats = Apartment::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN buildings.gender_restriction = "male" THEN 1 ELSE 0 END) as male,
            SUM(CASE WHEN buildings.gender_restriction = "female" THEN 1 ELSE 0 END) as female,
            MAX(apartments.updated_at) as last_update,
            MAX(CASE WHEN buildings.gender_restriction = "male" THEN apartments.updated_at END) as male_last_update,
            MAX(CASE WHEN buildings.gender_restriction = "female" THEN apartments.updated_at END) as female_last_update
        ')
        ->leftJoin('buildings', 'buildings.id', '=', 'apartments.building_id')
        ->first();

        return [
            'apartments' => [
                'count' => formatNumber($stats->total),
                'lastUpdateTime' => formatDate($stats->last_update),
            ],
            'apartments-male' => [
                'count' => formatNumber($stats->male),
                'lastUpdateTime' => formatDate($stats->male_last_update),
            ],
            'apartments-female' => [
                'count' => formatNumber($stats->female),
                'lastUpdateTime' => formatDate($stats->female_last_update),
            ],
        ];
}


    /**
     * Get apartment data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        // Join buildings to allow ordering/filtering by related fields
        $query = Apartment::query()
            ->leftJoin('buildings', 'buildings.id', '=', 'apartments.building_id')
            ->select('apartments.*',
                'buildings.number as building_number',
                'buildings.gender_restriction as building_gender_restriction'
            )
            ->with('building');

        $query = $this->applySearchFilters($query);

        return DataTables::of($query)
            ->addIndexColumn()
            // Computed/display columns
            ->addColumn('name', fn($apartment) => $apartment->formattedName)
            ->addColumn('building', fn($apartment) => $apartment->building?->formattedName)
            ->addColumn('total_rooms', fn($apartment) => formatNumber($apartment->total_rooms))
            ->addColumn('gender', fn($apartment) => $apartment->formattedGender)
            ->editColumn('active', fn($apartment) => $apartment->active ? __('active') : __('inactive'))
            ->editColumn('created_at', fn($apartment) => formatDate($apartment->created_at))
            ->addColumn('action', fn($apartment) => $this->renderActionButtons($apartment))
            // Ordering mappings aligned with frontend column names
            ->orderColumn('name', 'apartments.number $1')
            ->orderColumn('building', 'buildings.number $1')
            ->orderColumn('total_rooms', 'apartments.total_rooms $1')
            ->orderColumn('gender', 'buildings.gender_restriction $1')
            ->orderColumn('active', 'apartments.active $1')
            ->orderColumn('created_at', 'apartments.created_at $1')
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
        $searchActive = request('search_active');
        if (!empty($searchActive)) {
            $query->where('active', $searchActive);
        }
        $searchApartmentId = request('search_apartment_id');
        if (!empty($searchApartmentId)) {
            $query->where('apartments.id', $searchApartmentId);
        }

        $searchBuildingId = request('search_building_id');
        if (!empty($searchBuildingId)) {
            $query->where('building_id', $searchBuildingId);
        }
        $searchGenderRestriction = request('search_gender_restriction');
        if (!empty($searchGenderRestriction)) {
            // buildings table is joined in the main query
            $query->where('buildings.gender_restriction', $searchGenderRestriction);
        }
        return $query;
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param Apartment $apartment
     * @return string
     */
    protected function renderActionButtons($apartment): string
    {
        $singleActions = [];
        $singleActions[] = [
            'action' => $apartment->active ? 'deactivate' : 'activate',
            'icon' => $apartment->active ? 'bx bx-toggle-left' : 'bx bx-toggle-right',
            'class' => $apartment->active ? 'btn-warning' : 'btn-success',
            'label' => $apartment->active ? __('deactivate') : __('activate')
        ];
        $singleActions[] = [
            'action' => 'delete',
            'icon' => 'bx bx-trash',
            'class' => 'btn-danger',
            'label' => __('delete')
        ];
        return view('components.ui.datatable.table-actions', [
            'mode' => 'single',
            'singleActions' => $singleActions,
            'id' => $apartment->id,
            'type' => 'Apartment'
        ])->render();
    }

    /**
     * Set an apartment as active or inactive
     * @param int $id
     * @param bool $active
     * @return Apartment
     */
    public function setActive($id, bool $active): Apartment
    {
        $apartment = Apartment::findOrFail($id);
        DB::transaction(function () use ($apartment, $active) {
            $this->handleRoomsActivation($apartment, $active);
            $apartment->active = $active;
            $apartment->save();
        });
        return $apartment->fresh();
    }

    /**
     * Activate or deactivate all rooms of an apartment.
     *
     * @param \App\Models\Housing\Apartment $apartment
     * @param bool $active
     * @return void
     * @throws BusinessValidationException
     */
    private function handleRoomsActivation($apartment, bool $active)
    {
        foreach ($apartment->rooms as $room) {
            if ($room->current_occupancy > 0) {
                throw new BusinessValidationException(__("Cannot change activation status for room {$room->number} in apartment {$apartment->number} because it is currently occupied."));
            }
            $room->active = $active;
            $room->save();
        }
    }

    /**
     * Create rooms for an apartment.
     *
     * @param \App\Models\Housing\Apartment $apartment
     * @param int $totalRooms
     * @return void
     */
    private function createRoomsForApartment($apartment, int $totalRooms): void
    {
        for ($j = 1; $j <= $totalRooms; $j++) {
            $apartment->rooms()->create([
                'number' => (string)$j,
                'type' => 'single',
                'capacity' => 1,
                'current_occupancy' => 0,
                'available_capacity' => 1,
                'purpose' => 'housing',
                'occupancy_status' => 'available',
                'active' => true,
            ]);
        }
    }

    /**
     * Prepare update data for an apartment.
     *
     * @param Apartment $apartment
     * @param array $data
     * @return array
     */
    private function prepareUpdateData(Apartment $apartment, array $data): array
    {
        return [
            'number' => $data['number'] ?? $apartment->number,
            'building_id' => $data['building_id'] ?? $apartment->building_id,
            'active' => $data['active'] ?? $apartment->active,
        ];
    }
}