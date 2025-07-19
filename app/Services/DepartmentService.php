<?php

namespace App\Services;

use App\Models\Department;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class DepartmentService
{
    protected $model = Department::class;

    public function stats(): array
    {
        $stats = Department::selectRaw('
            COUNT(id) as total,
            MAX(updated_at) as last_update
        ')->first();

        return [
            'total' => [
                'count' => formatNumber($stats->total),
                'lastUpdateTime' => formatDate($stats->last_update),
            ],
        ];
    }

    public function create(array $data): Department
    {
        return DB::transaction(fn () => Department::create($data));
    }

    public function update($id, array $data): Department
    {
        return DB::transaction(function () use ($id, $data) {
            $dep = Department::findOrFail($id);
            $dep->update($data);
            return $dep;
        });
    }

    public function delete($id): bool
    {
        $dep = Department::find($id);
        if (!$dep) return false;
        $dep->delete();
        return true;
    }

    public function find($id): ?Department
    {
        return Department::find($id);
    }

    public function getAll()
    {
        return Department::get();
    }

    public function datatable(array $params)
    {
        $query = Department::query();
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('actions', fn($dep) =>
                view('components.ui.datatable.data-table-actions', [
                    'mode' => 'dropdown',
                    'id' => $dep->id,
                    'type' => 'Department',
                    'actions' => ['view', 'edit', 'delete'],
                ])->render()
            )
            ->editColumn('created_at', fn($dep) => formatDate($dep->created_at))
            ->rawColumns(['actions'])
            ->make(true);
    }
} 