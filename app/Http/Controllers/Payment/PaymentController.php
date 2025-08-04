<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\Payment\PaymentService;
use App\Models\Payment;
use App\Exceptions\BusinessValidationException;
use Exception;

class PaymentController extends Controller
{
    /**
     * PaymentController constructor.
     *
     * @param PaymentService $paymentService
     */
    public function __construct(protected PaymentService $paymentService)
    {}

    /**
     * Display the payment management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('payments.index');
    }

    /**
     * Get payment statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->paymentService->getStats();
            return successResponse('Stats fetched successfully.', $stats);
        } catch (Exception $e) {
            logError('PaymentController@stats', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get payment data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->paymentService->getDatatable();
        } catch (Exception $e) {
            logError('PaymentController@datatable', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Store a newly created payment.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->all();
            $payment = $this->paymentService->createPayment($validated);
            return successResponse('Payment created successfully.', $payment);
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('PaymentController@store', $e, ['request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Display the specified payment.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $payment = $this->paymentService->getPayment($id);
            return successResponse('Payment details fetched successfully.', $payment);
        } catch (Exception $e) {
            logError('PaymentController@show', $e, ['payment_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Update the specified payment.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->all();
            $payment = $this->paymentService->updatePayment(Payment::findOrFail($id), $validated);
            return successResponse('Payment updated successfully.', $payment);
        } catch (Exception $e) {
            logError('PaymentController@update', $e, ['payment_id' => $id, 'request' => $request->all()]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Remove the specified payment.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->paymentService->deletePayment($id);
            return successResponse('Payment deleted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('PaymentController@destroy', $e, ['payment_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get all payments (for dropdown and forms).
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $payments = $this->paymentService->getAll();
            return successResponse('Payments fetched successfully.', $payments);
        } catch (Exception $e) {
            logError('PaymentController@all', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get only the details array for a payment (AJAX modal).
     *
     * @param int $id
     * @return JsonResponse
     */
    public function details($id): JsonResponse
    {
        try {
            $details = $this->paymentService->getDetails($id);
            return successResponse('Payment details fetched successfully.', $details);
        } catch (Exception $e) {
            logError('PaymentController@details', $e, ['payment_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }
}
