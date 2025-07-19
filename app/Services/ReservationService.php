<?php

namespace App\Services;

use App\Models\Reservation;
use Yajra\DataTables\Facades\DataTables;

class ReservationService extends BaseService
{
    protected $model = Reservation::class;

    public function show($id)
    {
        $reservation = $this->find($id);
        if (!$reservation) {
            return null;
        }
        // Adjust fields as needed for reservations
        return [
            'id' => $reservation->id,
            'student' => $reservation->student->name ?? null,
            'room' => $reservation->room->number ?? null,
            'start_date' => $reservation->start_date,
            'end_date' => $reservation->end_date,
            'status' => $reservation->status,
            'active' => $reservation->active,
            'created_at' => formatDate($reservation->created_at),
            'updated_at' => formatDate($reservation->updated_at),
        ];
    }

    public function stats(): array
    {
        $stats = Reservation::selectRaw('
            COUNT(id) as total,
            COUNT(CASE WHEN active = 1 THEN 1 END) as active,
            COUNT(CASE WHEN active = 0 THEN 1 END) as inactive,
            MAX(updated_at) as last_update
        ')->first();

        return [
            'total' => [
                'count' => formatNumber($stats->total),
                'lastUpdateTime' => formatDate($stats->last_update),
            ],
            'active' => [
                'count' => formatNumber($stats->active),
                'lastUpdateTime' => formatDate($stats->last_update),
            ],
            'inactive' => [
                'count' => formatNumber($stats->inactive),
                'lastUpdateTime' => formatDate($stats->last_update),
            ],
        ];
    }

    public function datatable(array $params)
    {
        $query = Reservation::query();

        $this->applySearchFilters($query, $params);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('student', fn($reservation) => $reservation->student_name)
            ->editColumn('accommodation', function ($reservation) {
                if ($reservation->accommodation) {
                    if ($reservation->accommodation->room) {
                        return 'Room: ' . $reservation->accommodation->room->number;
                    } elseif ($reservation->accommodation->apartment) {
                        return 'Apartment: ' . $reservation->accommodation->apartment->number;
                    }
                    return 'Accommodation: ' . $reservation->accommodation->id;
                }
                return 'N/A';
            })
            ->editColumn('start_date', fn($reservation) => $reservation->start_date)
            ->editColumn('end_date', fn($reservation) => $reservation->end_date)
            ->editColumn('status', fn($reservation) => ucfirst($reservation->status))
            ->editColumn('active', fn($reservation) => $reservation->active ? 'Active' : 'Inactive')
            ->addColumn('actions', function ($reservation) {
                $isActive = $reservation->active;
                return view('components.ui.datatable.data-table-actions', [
                    'mode' => 'dropdown',
                    'id' => $reservation->id,
                    'type' => 'Reservation',
                    'actions' => ['view', 'edit', 'delete'],
                ])->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    private function applySearchFilters($query, array $params): void
    {
        // Join related tables for advanced search
        $query->join('users', 'reservations.user_id', '=', 'users.id')
              ->join('students', 'users.id', '=', 'students.user_id')
              ->join('accommodations', 'reservations.accommodation_id', '=', 'accommodations.id')
              ->leftJoin('rooms', function($join) {
                  $join->on('accommodations.accommodatable_id', '=', 'rooms.id')
                       ->where('accommodations.accommodatable_type', '=', \App\Models\Room::class);
              })
              ->leftJoin('apartments', function($join) {
                  $join->on('rooms.apartment_id', '=', 'apartments.id');
              })
              ->leftJoin('buildings', function($join) {
                  $join->on('apartments.building_id', '=', 'buildings.id');
              });

        if (!empty($params['search_student_name'])) {
            $query->where(function($q) use ($params) {
                $q->where('students.name_en', 'like', '%' . $params['search_student_name'] . '%')
                  ->orWhere('students.name_ar', 'like', '%' . $params['search_student_name'] . '%');
            });
        }
        if (!empty($params['search_student_national_id'])) {
            $query->where('students.national_id', 'like', '%' . $params['search_student_national_id'] . '%');
        }
        if (!empty($params['search_student_academic_id'])) {
            $query->where('students.academic_id', 'like', '%' . $params['search_student_academic_id'] . '%');
        }
        if (!empty($params['search_building_id'])) {
            $query->where('buildings.id', $params['search_building_id']);
        }
        if (!empty($params['search_apartment_number'])) {
            $query->where('apartments.number', $params['search_apartment_number']);
        }
        if (!empty($params['search_room_number'])) {
            $query->where('rooms.number', $params['search_room_number']);
        }
    }

    public function update($id, array $data): ?Reservation
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return null;
        }
        $updateData = [
            // Add reservation-specific update fields here
        ];
        $reservation->update($updateData);
        return $reservation->fresh(['student', 'room']);
    }
} 