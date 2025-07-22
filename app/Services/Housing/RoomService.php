<?php

namespace App\Services\Housing;

use App\Models\Room;
use App\Models\Apartment;
use App\Models\Building;
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
     */
    public function updateRoom(int $id, array $data): Room
    {
        $room = Room::findOrFail($id);
        $room->update([
            'type' => $data['type'] ?? $room->type,
            'purpose' => $data['purpose'] ?? $room->purpose,
            'description' => $data['description'] ?? $room->description,
        ]);
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
     * @param int|null $apartmentId
     * @return array
     */
    public function getAll(int $apartmentId = null): array
    {
        return Room::when($apartmentId, function ($query, $apartmentId) {
                return $query->where('apartment_id', $apartmentId);
            })
            ->select(['id', 'number'])
            ->get()
            ->map(fn ($room) => [
                'id' => $room->id,
                'number' => $room->number,
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
        $rooms = Room::with('apartment.building')->get();

        $total = $rooms->count();
        $maleRooms = $rooms->filter(function ($room) {
            return $room->apartment->building->gender_restriction === 'male';
        });
        $femaleRooms = $rooms->filter(function ($room) {
            return $room->apartment->building->gender_restriction === 'female';
        });

        $lastUpdate = $rooms->max('updated_at');
        $maleLastUpdate = $maleRooms->max('updated_at');
        $femaleLastUpdate = $femaleRooms->max('updated_at');

        return [
            'total' => [
                'count' => formatNumber($total),
                'lastUpdateTime' => formatDate($lastUpdate),
            ],
            'male' => [
                'count' => formatNumber($maleRooms->count()),
                'lastUpdateTime' => formatDate($maleLastUpdate),
            ],
            'female' => [
                'count' => formatNumber($femaleRooms->count()),
                'lastUpdateTime' => formatDate($femaleLastUpdate),
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
        // Use joins to make related columns available for sorting
        $query = Room::select([
                'rooms.*',
                'apartments.number as apartment_number',
                'buildings.number as building_number',
                'buildings.gender_restriction as building_gender_restriction'
            ])
            ->join('apartments', 'rooms.apartment_id', '=', 'apartments.id')
            ->join('buildings', 'apartments.building_id', '=', 'buildings.id');
        
        // Apply search filters to the query builder
        $query = $this->applySearchFilters($query);
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('number', fn($room) => 'Room ' . $room->number)
            ->editColumn('apartment_number', fn($room) => 'Apartment ' . $room->apartment_number)
            ->editColumn('building_number', fn($room) => 'Building ' . $room->building_number)
            ->editColumn('type', fn($room) => $room->type)
            ->editColumn('purpose', fn($room) => snakeToNormalCase($room->purpose))
            ->editColumn('building_gender_restriction', fn($room) => ucfirst($room->building_gender_restriction))
            ->editColumn('active', fn($room) => $room->active ? 'Active' : 'Inactive')
            ->editColumn('occupancy_status', fn($room) => ucfirst($room->occupancy_status))
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
    protected function applySearchFilters($query): Builder
    {
        $searchApartment = request('search_apartment_number');
        if (!empty($searchApartment)) {
            $query->where('apartments.number', $searchApartment);
        }

        $searchBuilding = request('search_building_id');
        if (!empty($searchBuilding)) {
            $query->where('apartments.building_id', $searchBuilding);
        }

        $searchGender = request('search_gender_restriction');
        if (!empty($searchGender)) {
            $query->where('buildings.gender_restriction', $searchGender);
        }

        $searchActive = request('search_active');
        if (!empty($searchActive)) {
            $query->where('rooms.active', $searchActive);
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
            'label' => $room->active ? 'Deactivate' : 'Activate'
        ];
        return view('components.ui.datatable.data-table-actions', [
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