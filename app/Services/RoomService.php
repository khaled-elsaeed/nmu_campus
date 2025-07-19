<?php

namespace App\Services;

use App\Models\Room;
use Yajra\DataTables\Facades\DataTables;

class RoomService extends BaseService
{
    /**
     * The model associated with the service.
     *
     * @var string
     */
    protected $model = Room::class;


    /**
     * Show the details of a specific room by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function show($id)
    {
        $room = $this->find($id);
        if (!$room) {
            return null;
        }

        return [
            'id' => $room->id,
            'number' => $room->number,
            'apartment' => $room->apartment->number,
            'apartment_id' => $room->apartment_id,
            'building' => $room->apartment->building->number,
            'building_id' => $room->apartment->building_id,
            'gender_restriction' => $room->apartment->building->gender_restriction,
            'type' => $room->type,
            'purpose' => $room->purpose,
            'active' => $room->active,
            'capacity' => $room->capacity,
            'current_occupancy' => $room->current_occupancy,
            'available_capacity' => $room->available_capacity,
            'description' => $room->description,
            'created_at' => formatDate($room->created_at),
            'updated_at' => formatDate($room->updated_at),
        ];
    }

    /**
     * Get statistics for rooms, grouped by gender restriction.
     *
     * @return array<string, array<string, string|null>>
     */
    public function stats(): array
    {
        $stats = Room::join('apartments', 'apartments.id', '=', 'rooms.apartment_id')
            ->join('buildings', 'buildings.id', '=', 'apartments.building_id')
            ->selectRaw('
                COUNT(rooms.id) as total,
                COUNT(CASE WHEN buildings.gender_restriction = "male" THEN 1 END) as male,
                COUNT(CASE WHEN buildings.gender_restriction = "female" THEN 1 END) as female,
                MAX(rooms.updated_at) as last_update,
                MAX(CASE WHEN buildings.gender_restriction = "male" THEN rooms.updated_at END) as male_last_update,
                MAX(CASE WHEN buildings.gender_restriction = "female" THEN rooms.updated_at END) as female_last_update
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
     * Get a DataTable response for rooms with optional filters.
     *
     * @param array $params
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function datatable(array $params)
    {
        $query = Room::query()
            ->leftJoin('apartments', 'rooms.apartment_id', '=', 'apartments.id')
            ->leftJoin('buildings', 'apartments.building_id', '=', 'buildings.id')
            ->select('rooms.*', 'rooms.number as room_number', 'apartments.number as apartment_number', 'buildings.number as building_number', 'buildings.gender_restriction as building_gender_restriction');

        $this->applySearchFilters($query, $params);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('number', function ($room) {
                return 'Room ' . $room->room_number;
            })
            ->editColumn('apartment', function ($room) {
                return 'Apartment ' . $room->apartment_number;
            })
            ->editColumn('building', function ($room) {
                return 'Building ' . $room->building_number;
            })
            ->editColumn('type', fn($room) => $room->type)
            ->editColumn('purpose', function($room) {
                return snakeToNormalCase($room->purpose);
            })
            ->editColumn('gender_restriction', function($room) {
                return ucfirst($room->building_gender_restriction);
            })
            ->editColumn('active', fn($room) => $room->active ? 'Active' : 'Inactive')
            ->addColumn('actions', function ($room) {
                $isActive = $room->active;
                return view('components.ui.datatable.data-table-actions', [
                    'mode' => 'both',
                    'id' => $room->id,
                    'type' => 'Room',
                    'actions' => ['view', 'edit', 'delete'],
                    'singleIcon' => $isActive ? 'bx-x' : 'bx-check',
                    'singleAction' => $isActive ? 'deactivate' : 'activate',
                    'singleLabel' => $isActive ? 'Deactivate' : 'Activate',
                ])->render();
            })
            ->rawColumns(['actions'])
            ->orderColumn('building_number', function ($query, $order) {
                $query->orderByRaw('CAST(building_number AS UNSIGNED) ' . $order);
            })
            ->orderColumn('apartment_number', function ($query, $order) {
                $query->orderByRaw('CAST(apartment_number AS UNSIGNED) ' . $order);
            })
            ->orderColumn('room_number', function ($query, $order) {
                $query->orderByRaw('CAST(room_number AS UNSIGNED) ' . $order);
            })
            ->make(true);
    }

    /**
     * Apply search filters to the query based on provided parameters.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $params
     * @return void
     */
    private function applySearchFilters($query, array $params): void
    {
        if (!empty($params['search_apartment_number'])) {
            $query->where('apartments.number', $params['search_apartment_number']);
        }
        if (!empty($params['search_building_id'])) {
            $query->where('apartments.building_id', $params['search_building_id']);
        }
        if (!empty($params['search_gender_restriction'])) {
            $query->where('buildings.gender_restriction', $params['search_gender_restriction']);
        }
    }

    /**
     * Update a room by ID with the provided data.
     *
     * @param int $id
     * @param array $data
     * @return Room|null
     */
    public function update($id, array $data): ?Room
    {
        $room = Room::find($id);
        if (!$room) {
            return null;
        }
        $updateData = [
            'type' => $data['type'] ?? $room->type,
            'purpose' => $data['purpose'] ?? $room->purpose,
            'description' => $data['description'] ?? $room->description,
        ];
        $room->update($updateData);
        return $room->fresh('apartment');
    }

    /**
     * Get all rooms as id and number for dropdowns/selects.
     *
     * @return array
     */
    public function all($apartmentId = null)
    {
        $query = Room::query();
        if ($apartmentId) {
            $query->where('apartment_id', $apartmentId);
        }
        return $query->get();
    }

} 