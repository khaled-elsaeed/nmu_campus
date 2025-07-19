<?php

namespace App\Services;

use App\Models\StaffCategory;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class StaffCategoryService
{
    protected $model = StaffCategory::class;

    public function stats(): array
    {
        $stats = StaffCategory::selectRaw('
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
            ],
            'inactive' => [
                'count' => formatNumber($stats->inactive),
            ],
        ];
    }

    public function create(array $data): StaffCategory
    {
        return DB::transaction(fn () => StaffCategory::create($data));
    }

    public function update($id, array $data): StaffCategory
    {
        return DB::transaction(function () use ($id, $data) {
            $cat = StaffCategory::findOrFail($id);
            $cat->update($data);
            return $cat;
        });
    }

    public function delete($id): bool
    {
        $cat = StaffCategory::find($id);
        if (!$cat) return false;
        $cat->delete();
        return true;
    }

    public function find($id): ?StaffCategory
    {
        return StaffCategory::find($id);
    }

    public function getAll()
    {
        return StaffCategory::get();
    }

    public function datatable(array $params)
    {
        $query = StaffCategory::query();
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('actions', fn($cat) =>
                view('components.ui.datatable.data-table-actions', [
                    'mode' => 'dropdown',
                    'id' => $cat->id,
                    'type' => 'StaffCategory',
                    'actions' => ['view', 'edit', 'delete'],
                ])->render()
            )
            ->editColumn('created_at', fn($cat) => formatDate($cat->created_at))
            ->rawColumns(['actions'])
            ->make(true);
    }
} 