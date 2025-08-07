<?php

namespace App\Http\Controllers\Academic;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\Academic\AcademicTermService;
use App\Models\Academic\AcademicTerm;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\AcademicTermStoreRequest;
use App\Http\Requests\Academic\AcademicTermUpdateRequest;

class AcademicTermController extends Controller
{
    /**
     * AcademicTermController constructor.
     *
     * @param AcademicTermService $academicTermService
     */
    public function __construct(protected AcademicTermService $academicTermService)
    {}

    /**
     * Display the term management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('academic.academic_term');
    }

    /**
     * Get term statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->academicTermService->getStats();
            return successResponse(__('academic_terms.messages.success.stats_fetched'), $stats);
        } catch (Exception $e) {
            logError('AcademicTermController@stats', $e);
            return errorResponse(__('academic_terms.messages.error.internal_server'), [], 500);
        }
    }

    /**
     * Get term data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->academicTermService->getDatatable();
        } catch (Exception $e) {
            logError('AcademicTermController@datatable', $e);
            return errorResponse(__('general.messages.error.server_error'), [], 500);
        }
    }

    /**
     * Store a newly created term.
     *
     * @param AcademicTermStoreRequest $request
     * @return JsonResponse
     */
    public function store(AcademicTermStoreRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $term = $this->academicTermService->createTerm($validated);
            return successResponse(__('academic_terms.messages.success.created'), $term);
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('AcademicTermController@store', $e, ['request' => $request->all()]);
            return errorResponse(__('general.messages.error.server_error'), [], 500);
        }
    }

    /**
     * Display the specified term.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $term = $this->academicTermService->getTerm($id);
            return successResponse('Term details fetched successfully.', $term);
        } catch (Exception $e) {
            logError('AcademicTermController@show', $e, ['term_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Update the specified term.
     *
     * @param AcademicTermUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(AcademicTermUpdateRequest $request, $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $term = $this->academicTermService->updateTerm($id, $validated);
            return successResponse(__('academic_terms.messages.success.updated'), $term);
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('AcademicTermController@update', $e, ['term_id' => $id, 'request' => $request->all()]);
            return errorResponse(__('general.messages.error.server_error'), [], 500);
        }
    }

    /**
     * Remove the specified term.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->academicTermService->deleteTerm($id);
            return successResponse('Term deleted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('AcademicTermController@destroy', $e, ['term_id' => $id]);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get all terms (for dropdown and forms).
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $terms = $this->academicTermService->getAll();
            return successResponse(__('academic_terms.messages.success.fetched'), $terms);
        } catch (Exception $e) {
            logError('AcademicTermController@all', $e);
            return errorResponse(__('general.messages.error.server_error'), [], 500);
        }
    }

    /**
     * Get all terms (for dropdown and forms).
     *
     * @return JsonResponse
     */
    public function allWithInactive(): JsonResponse
    {
        try {
            $terms = $this->academicTermService->getAllWithInactive();
            return successResponse('Terms fetched successfully.', $terms);
        } catch (Exception $e) {
            logError('AcademicTermController@all', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Start a term (activate reservations and set as current)
     * @param int $id
     * @return JsonResponse
     */
    public function startTerm($id): JsonResponse
    {
        try {
            $term = $this->academicTermService->start($id);
            $activatedCount = isset($term->activated_reservations_count) ? $term->activated_reservations_count : 0;
            $message = $this->generateStartTermMessage($activatedCount);
            return successResponse($message, $term);
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('AcademicTermController@startTerm', $e, ['term_id' => $id]);
            return errorResponse(__('academic_terms.messages.error.start_failed'), [], 500);
        }
    }

    /**
     * Generate a message for starting a term based on activated reservations count.
     *
     * @param int $activatedCount
     * @return string
     */
    private function generateStartTermMessage(int $activatedCount): string
    {
        $message = __('academic_terms.messages.success.started');
        if ($activatedCount > 0) {
            $message .= " " . __('academic_terms.messages.success.reservations_activated', ['count' => $activatedCount]);
        } else {
            $message .= " " . __('academic_terms.messages.success.no_reservations_activated');
        }
        return $message;
    }

    /**
     * End a term (set current = false)
     * @param int $id
     * @return JsonResponse
     */
    public function endTerm($id): JsonResponse
    {
        try {
            $term = $this->academicTermService->end($id);
            return successResponse(__('academic_terms.messages.success.ended'), $term);
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('AcademicTermController@endTerm', $e, ['term_id' => $id]);
            return errorResponse(__('academic_terms.messages.error.end_failed'), [], 500);
        }
    }

    /**
     * Activate a term (set active = true)
     * @param int $id
     * @return JsonResponse
     */
    public function activate($id): JsonResponse
    {
        try {
            $term = $this->academicTermService->setActive($id, true);
            return successResponse(__('academic_terms.messages.success.activated'), $term);
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('AcademicTermController@activateTerm', $e, ['term_id' => $id]);
            return errorResponse(__('academic_terms.messages.error.activate_failed'), [], 500);
        }
    }

    /**
     * Deactivate a term (set active = false)
     * @param int $id
     * @return JsonResponse
     */
    public function deactivate($id): JsonResponse
    {
        try {
            $term = $this->academicTermService->setActive($id, false);
            return successResponse(__('academic_terms.messages.success.deactivated'), $term);
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], $e->getCode());
        } catch (Exception $e) {
            logError('AcademicTermController@deactivateTerm', $e, ['term_id' => $id]);
            return errorResponse(__('academic_terms.messages.error.deactivate_failed'), [], 500);
        }
    }
} 