<?php

namespace App\Http\Controllers\Geography;

use App\Models\Governorate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GovernorateController extends Controller
{
    public function all(): JsonResponse
    {
        $governorates = Governorate::all();
        return response()->json([
            'success' => true,

            'message' => 'Governorates retrieved successfully.',
            'data' => $governorates
        ]);
    }
} 