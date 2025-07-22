<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\Payment\InsuranceService;
use App\Models\Insurance;
use App\Exceptions\BusinessValidationException;
use Exception;

class InsuranceController extends Controller
{
    /**
     * InsuranceController constructor.
     *
     * @param InsuranceService $insuranceService
     */
    public function __construct(protected InsuranceService $insuranceService)
    {}

    /**
     * Display the insurance management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('payments.insurance');
    }

    /**
     * Get insurance statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->insuranceService->getStats();
            return successResponse('Stats fetched successfully.', $stats);
        } catch (Exception $e) {
            logError('InsuranceController@stats', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get insurance data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->insuranceService->getDatatable();
        } catch (Exception $e) {
            logError('InsuranceController@datatable', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Store a newly created insurance record.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->only(['reservation_number', 'amount', 'status']);
            $insurance = $this->insuranceService->createInsurance($validated);
            return successResponse('Insurance created successfully.', $insurance);
        } catch (Exception $e) {
            logError('InsuranceController@store', $e, ['request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Display the specified insurance record.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $insurance = $this->insuranceService->getInsurance($id);
            return successResponse('Insurance details fetched successfully.', $insurance);
        } catch (Exception $e) {
            logError('InsuranceController@show', $e, ['insurance_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Update the specified insurance record.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->only(['reservation_number', 'amount', 'status']);
            $insurance = $this->insuranceService->updateInsurance(Insurance::findOrFail($id), $validated);
            return successResponse('Insurance updated successfully.', $insurance);
        } catch (Exception $e) {
            logError('InsuranceController@update', $e, ['insurance_id' => $id, 'request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Remove the specified insurance record.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->insuranceService->deleteInsurance($id);
            return successResponse('Insurance deleted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('InsuranceController@destroy', $e, ['insurance_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get all insurances (for dropdown and forms).
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $insurances = $this->insuranceService->getAll();
            return successResponse('Insurances fetched successfully.', $insurances);
        } catch (Exception $e) {
            logError('InsuranceController@all', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Cancel the specified insurance.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function cancel($id): JsonResponse
    {
        try {
            $this->insuranceService->cancel($id);
            return successResponse('Insurance cancelled successfully.');
        } catch (Exception $e) {
            logError('InsuranceController@cancel', $e, ['insurance_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Refund the specified insurance.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function refund($id): JsonResponse
    {
        try {
            $this->insuranceService->refund($id);
            return successResponse('Insurance refunded successfully.');
        } catch (Exception $e) {
            logError('InsuranceController@refund', $e, ['insurance_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }
}
