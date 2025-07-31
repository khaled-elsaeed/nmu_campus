<?php

namespace App\Http\Controllers\Geography;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use Illuminate\Http\JsonResponse;

class GovernorateController extends Controller
{
    public function all(): JsonResponse
    {
        try {
            $governorates = Governorate::all();
            return response()->json([
                'success' => true,
                'message' => 'Governorates retrieved successfully.',
                'data' => $governorates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve governorates.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}