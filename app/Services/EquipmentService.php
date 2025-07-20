<?php

namespace App\Services;

use App\Models\Equipment;
use App\Exceptions\BusinessValidationException;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class EquipmentService
{
    public function getAll()
    {
        return Equipment::orderBy('name_en')->get();
    }
} 