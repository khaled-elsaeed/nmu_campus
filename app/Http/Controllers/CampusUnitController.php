<?php

namespace App\Http\Controllers;

use App\Models\CampusUnit;
use Illuminate\Http\JsonResponse;

class CampusUnitController extends Controller
{
    /**
     * Get all campus units for dropdowns.
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $campusUnits = CampusUnit::select('id', 'name_en', 'name_ar')
                ->orderBy('name_en')
                ->get()
                ->map(function ($campusUnit) {
                    return [
                        'id' => $campusUnit->id,
                        'name' => $campusUnit->name_en,
                        'name_ar' => $campusUnit->name_ar,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $campusUnits
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch campus units: ' . $e->getMessage()
            ], 500);
        }
    }
} 