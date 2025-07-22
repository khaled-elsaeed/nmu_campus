<?php

namespace App\Services;

use App\Models\Equipment;
use App\Exceptions\BusinessValidationException;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class EquipmentService
{
    /**
     * Get all equipment.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Equipment::orderBy('name_en')->get(['id', 'name_en', 'name_ar']);
    }

    /**
     * Get a single equipment item by ID.
     * @param int $id
     * @return array
     */
    public function getEquipment(int $id): array
    {
        $equipment = Equipment::select(['id', 'name_en', 'name_ar', 'is_active'])->find($id);

        if (!$equipment) {
            throw new BusinessValidationException('Equipment not found.');
        }

        return [
            'id' => $equipment->id,
            'name_en' => $equipment->name_en,
            'name_ar' => $equipment->name_ar,
            'is_active' => $equipment->is_active,
        ];
    }
} 