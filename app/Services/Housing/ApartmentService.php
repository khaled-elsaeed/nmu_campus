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
            throw new BusinessValidationException(__('apartments.messages.not_found'));
        }

        return [
            'id' => $apartment->id,
            'number' => $apartment->number,
            'building_id' => $apartment->building_id,
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
        
        if ($apartment->residents()->count() > 0) {
            throw new BusinessValidationException(__('apartments.messages.cannot_delete_has_residents'));
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
        $total = Apartment::count();
        $male = Apartment::whereHas('building', function ($q) {
            $q->where('gender_restriction', 'male');
        })->count();
        $female = Apartment::whereHas('building', function ($q) {
            $q->where('gender_restriction', 'female');
        })->count();

        $lastUpdate = Apartment::max('updated_at');
        $maleLastUpdate = Apartment::whereHas('building', function ($q) {
            $q->where('gender_restriction', 'male');
        })->max('updated_at');
        $femaleLastUpdate = Apartment::whereHas('building', function ($q) {
            $q->where('gender_restriction', 'female');
        })->max('updated_at');

        return [
            'apartments' => [
                'count' => formatNumber($total),
                'lastUpdateTime' => formatDate($lastUpdate),
            ],
            'apartments-male' => [
                'count' => formatNumber($male),
                'lastUpdateTime' => formatDate($maleLastUpdate),
            ],
            'apartments-female' => [
                'count' => formatNumber($female),
                'lastUpdateTime' => formatDate($femaleLastUpdate),
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
        $query = Apartment::select([
            'apartments.*',
            'buildings.number as building_number',
            'buildings.gender_restriction as building_gender_restriction'
        ])
        ->join('buildings', 'apartments.building_id', '=', 'buildings.id');

        $query = $this->applySearchFilters($query);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('number', fn($apartment) => 'Apartment ' . $apartment->number)
            ->addColumn('building', fn($apartment) => 'Building ' . $apartment->building_number)
            ->addColumn('gender_restriction', fn($apartment) => $apartment->building_gender_restriction)
            ->editColumn('active', fn($apartment) => $apartment->active ? 'Active' : 'Inactive')
            ->editColumn('created_at', fn($apartment) => formatDate($apartment->created_at))
            ->addColumn('action', fn($apartment) => $this->renderActionButtons($apartment))
            ->orderColumn('number', 'apartments.number $1')
            ->orderColumn('building_number', 'buildings.number $1')
            ->orderColumn('building_gender_restriction', 'buildings.gender_restriction $1')
            ->orderColumn('active', 'apartments.active $1')
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
            $query->whereHas('building', function ($q) use ($searchGenderRestriction) {
                $q->where('buildings.gender_restriction', $searchGenderRestriction);
            });
        }
        return $query;
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param Apartment $apartment
     * @return string
     */
    public function renderActionButtons(Apartment $apartment): string
    {
        return view('components.ui.datatable.table-actions', [
            'mode' => 'dropdown',
            'actions' => ['view', 'edit', 'delete'],
            'id' => $apartment->id,
            'type' => __('apartments.table.type'),
            'singleActions' => []
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
     */
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