<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\EquipmentService;
use App\Models\Equipment;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Requests\Resident\EquipmentStoreRequest;
use App\Http\Requests\Resident\EquipmentUpdateRequest;

class EquipmentController extends Controller
{
    /**
     * EquipmentController constructor.
     *
     * @param EquipmentService $equipmentService
     */
    public function __construct(protected EquipmentService $equipmentService)
    {}

    
    /**
     * Get all equipment (for dropdowns/forms).
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $equipment = $this->equipmentService->getAll();
            return successResponse('Equipment fetched successfully.', $equipment);
        } catch (Exception $e) {
            logError('EquipmentController@all', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }
}
