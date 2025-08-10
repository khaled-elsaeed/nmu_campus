<?php

namespace App\Services\Housing;

use App\Models\Housing\Room;
use App\Models\Housing\Apartment;
use App\Models\Housing\Building;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RoomService
{
    /**
     * Create a new room.
     *
     * @param array $data
     * @return Room
     */
    public function createRoom(array $data): Room
    {
        $room = Room::create($data);
        return $room->fresh('apartment.building');
    }

    /**
     * Update an existing room by ID.
     *
     * @param int $id
     * @param array $data
     * @return Room
     * @throws BusinessValidationException
     */
    public function updateRoom(int $id, array $data): Room
    {
        $room = Room::findOrFail($id);
        
        $newType = $data['type'] ?? $room->type;
        $isTypeChanging = $newType !== $room->type;
        
        // Determine new capacity based on type
        $newCapacity = ($newType === 'double') ? 2 : 1;
        
        
        // If changing from double to single, check current occupancy
        if ($isTypeChanging && $room->type === 'double' && $newType === 'single') {
            if ($room->current_occupancy > 1) {
                throw new BusinessValidationException(
                    "Cannot change room #{$room->number} from double to single: Room currently has {$room->current_occupancy} occupants. Please relocate occupants first or ensure only 1 occupant remains."
                );
            }
        }
        
        
        // Calculate new available capacity
        $newAvailableCapacity = $newCapacity - $room->current_occupancy;
        
        // Update the room
        $room->update([
            'type' => $newType,
            'capacity' => $newCapacity,
            'available_capacity' => $newAvailableCapacity,
            'purpose' => $data['purpose'] ?? $room->purpose,
            'description' => $data['description'] ?? $room->description,
        ]);
        
        // Refresh the room to get updated values from database
        $room->refresh();
        
        
        
        return $room->fresh('apartment.building');
    }



    /**
     * Get a single room with its apartment and building.
     *
     * @param int $id
     * @return array
     */
    public function getRoom(int $id): array
    {
        $room = Room::select([
            'id', 
            'number', 
            'type',
            'purpose',
            'description',
        ])->find($id);

        if (!$room) {
            throw new BusinessValidationException('Room not found.');
        }

        return [
            'id' => $room->id,
            'number' => $room->number,
            'type' => $room->type,
            'purpose' => $room->purpose,
            'description' => $room->description
        ];
    }

    /**
     * Delete a room.
     *
     * @param int $id
     * @return void
     * @throws BusinessValidationException
     */
    public function deleteRoom($id): void
    {
        $room = Room::findOrFail($id);
        if ($room->current_occupancy > 0) {
            throw new BusinessValidationException(
                "Cannot delete room: Room #{$room->number} is currently occupied."
            );
        }
        $room->delete();
    }

    /**
     * Get all rooms, optionally filtered by apartment ID.
     *
     * @param int $apartmentId
     * @return array
     */
    public function getAll(int $apartmentId): array
    {
        return Room::where('apartment_id', $apartmentId)
            ->select(['id', 'number','type'])
            ->get()
            ->map(fn ($room) => [
                'id' => $room->id,
                'number' => $room->number,
                'type' => $room->type,
            ])
            ->toArray();
    }

    /**
     * Get room statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $stats = Room::join('apartments', 'rooms.apartment_id', '=', 'apartments.id')
            ->join('buildings', 'apartments.building_id', '=', 'buildings.id')
            ->selectRaw('
                COUNT(*) as total_count,
                SUM(rooms.capacity) as total_beds_count,
                SUM(CASE WHEN buildings.gender_restriction = "male" THEN 1 ELSE 0 END) as male_count,
                SUM(CASE WHEN buildings.gender_restriction = "male" THEN rooms.capacity ELSE 0 END) as male_beds_count,
                SUM(CASE WHEN buildings.gender_restriction = "male" AND rooms.type = "double" THEN 1 ELSE 0 END) as male_count_double_rooms,
                SUM(CASE WHEN buildings.gender_restriction = "female" THEN 1 ELSE 0 END) as female_count,
                SUM(CASE WHEN buildings.gender_restriction = "female" THEN rooms.capacity ELSE 0 END) as female_beds_count,
                SUM(CASE WHEN buildings.gender_restriction = "female" AND rooms.type = "double" THEN 1 ELSE 0 END) as female_count_double_rooms,
                SUM(CASE WHEN rooms.type = "double" THEN 1 ELSE 0 END) as total_double_rooms_count,
                SUM(CASE WHEN rooms.purpose = "housing" AND rooms.active = 1 THEN rooms.capacity ELSE 0 END) as available_beds_count,
                SUM(CASE WHEN rooms.purpose = "housing" AND rooms.active = 1 AND buildings.gender_restriction = "male" THEN rooms.capacity ELSE 0 END) as available_male_beds_count,
                SUM(CASE WHEN rooms.purpose = "housing" AND rooms.active = 1 AND buildings.gender_restriction = "female" THEN rooms.capacity ELSE 0 END) as available_female_beds_count,
                MAX(rooms.updated_at) as last_update,
                MAX(CASE WHEN buildings.gender_restriction = "male" THEN rooms.updated_at END) as male_last_update,
                MAX(CASE WHEN buildings.gender_restriction = "female" THEN rooms.updated_at END) as female_last_update
            ')
            ->first();

        return [
            'rooms' => [
                'count' => formatNumber($stats->total_count ?? 0),
                'male' => formatNumber($stats->male_count ?? 0),
                'female' => formatNumber($stats->female_count ?? 0),
                'lastUpdateTime' => formatDate($stats->last_update),
            ],
            'beds' => [
                'count' => formatNumber($stats->total_beds_count ?? 0),
                'male' => formatNumber($stats->male_beds_count ?? 0),
                'female' => formatNumber($stats->female_beds_count ?? 0),
                'lastUpdateTime' => formatDate($stats->last_update), 
            ],
            'double-rooms' => [ 
                'count' => formatNumber($stats->total_double_rooms_count ?? 0),
                'male' => formatNumber($stats->male_count_double_rooms ?? 0), 
                'female' => formatNumber($stats->female_count_double_rooms ?? 0), 
                'lastUpdateTime' => formatDate($stats->last_update), 
            ],
            'available-beds' => [
                'count' => formatNumber($stats->available_beds_count ?? 0),
                'male' => formatNumber($stats->available_male_beds_count ?? 0),
                'female' => formatNumber($stats->available_female_beds_count ?? 0),
                'lastUpdateTime' => formatDate($stats->last_update),
            ],
        ];
    }

    /**
     * Get room data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $query = Room::query();

        // Apply search filters to the query builder
        $query = $this->applySearchFilters($query);
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name', fn($room) => $room->formatted_name)
            ->addColumn('apartment', fn($room) => $room->apartment?->formatted_name)
            ->addColumn('building', fn($room) => $room->apartment?->building?->formatted_name)
            ->editColumn('type', fn($room) => __("general.{$room->type}"))
            ->editColumn('purpose', fn($room) => __("general.{$room->purpose}"))
            ->editColumn('gender', fn($room) => __("general.{$room->gender}"))
            ->editColumn('active', fn($room) => $room->active ? __('general.active') : __('general.inactive'))
            ->editColumn('occupancy_status', fn($room) => $room->occupancy_status ? ucfirst($room->occupancy_status) : null)
            ->addColumn('action', fn($room) => $this->renderActionButtons($room))
            ->orderColumn('number', 'rooms.number $1')
            ->orderColumn('apartment_number', 'apartments.number $1')
            ->orderColumn('building_number', 'buildings.number $1')
            ->orderColumn('type', 'rooms.type $1')
            ->orderColumn('purpose', 'rooms.purpose $1')
            ->orderColumn('building_gender_restriction', 'buildings.gender_restriction $1')
            ->orderColumn('active', 'rooms.active $1')
            ->orderColumn('occupancy_status', 'rooms.occupancy_status $1')
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Apply search filters to the query.
     *
     * @param Builder $query
     * @return Builder
     */
    protected function applySearchFilters(Builder $query): Builder
    {
        // Filter by apartment
        if ($searchApartment = request('search_apartment_id')) {
            $query->where('apartment_id', $searchApartment);
        }

        // Filter by building
        if ($searchBuilding = request('search_building_id')) {
            $query->whereHas('apartment.building', function (Builder $q) use ($searchBuilding) {
                $q->where('id', $searchBuilding);
            });
        }

        // Filter by gender restriction
        if ($searchGender = request('search_gender_restriction')) {
            $query->whereHas('apartment.building', function (Builder $q) use ($searchGender) {
                $q->where('gender_restriction', $searchGender);
            });
        }

        // Filter by active status
        if ($searchActive = request('search_active')) {
            $isActive = filter_var($searchActive, FILTER_VALIDATE_BOOLEAN);
            $query->where('active', $isActive);
        }

        return $query;
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param Room $room
     * @return string
     */
    public function renderActionButtons($room): string
    {
        $actions = ['edit', 'delete'];
        $singleActions = [];
        $singleActions[] = [
            'action' => $room->active ? 'deactivate' : 'activate',
            'icon' => $room->active ? 'bx bx-toggle-left' : 'bx bx-toggle-right',
            'class' => $room->active ? 'btn-warning' : 'btn-success',
            'label' => $room->active ? __('rooms.buttons.deactivate') : __('rooms.buttons.activate')
        ];
        return view('components.ui.datatable.table-actions', [
            'mode' => 'both',
            'actions' => $actions,
            'id' => $room->id,
            'type' => 'Room',
            'singleActions' => $singleActions
        ])->render();
    }

    /**
     * Set a room as active or inactive
     * @param int $id
     * @param bool $active
     * @return Room
     */
    public function setActive($id, bool $active): Room
    {
        $room = Room::with('apartment')->findOrFail($id);
        if ($room->current_occupancy > 0 && !$active) {
            throw new BusinessValidationException("Cannot deactivate room #{$room->number} because it is currently occupied.");
        }
        $room->active = $active;
        $room->save();
        return $room->fresh('apartment.building');
    }
}