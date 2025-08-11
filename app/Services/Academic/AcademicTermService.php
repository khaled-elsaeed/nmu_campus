<?php

namespace App\Services\Academic;

use App\Models\Academic\AcademicTerm;
use App\Models\User;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReservationActivated;

class AcademicTermService
{
    /**
     * Create a new term.
     *
     * @param array $data
     * @return AcademicTerm
     */
    public function createTerm(array $data): AcademicTerm
    {
        $code = $this->generateCode($data['season'], $data['year']);
        if (AcademicTerm::where('code', $code)->exists()) {
            throw new BusinessValidationException(__(':field already exists.', ['field' => __('code')]));
        }
        return AcademicTerm::create([
            'season' => $data['season'],
            'year' => $data['year'],
            'code' => $code,
            'semester_number' => $data['semester_number'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'active' => false,
            'current' => false,
        ]);
    }

    /**
     * Update an existing term.
     *
     * @param int $id
     * @param array $data
     * @return AcademicTerm
     */
    public function updateTerm(int $id, array $data): AcademicTerm
    {
        $term = AcademicTerm::findOrFail($id);
        $code = $this->generateCode($data['season'], $data['year']);
        if (AcademicTerm::where('code', $code)->where('id', '!=', $term->id)->exists()) {
            throw new BusinessValidationException(__(':field already exists.', ['field' => __('code')]));
        }
        $updateData = [
            'season' => $data['season'],
            'year' => $data['year'],
            'code' => $code,
            'semester_number' => $data['semester_number'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
        ];
        $term->update($updateData);
        return $term->fresh();
    }

    /**
     * Retrieve a single academic term along with its reservations.
     *
     * @param int $id
     * @return AcademicTerm
     */
    public function getTerm(int $id): AcademicTerm
    {
        $term = AcademicTerm::withCount('reservations')->find($id);

        if (!$term) {
            throw new BusinessValidationException(__(':field not found.', ['field' => __('term')]));
        }

        // Add formatted date attributes
        $term->start_date_formatted = isset($term->start_date) ? formatDate($term->start_date) : null;
        $term->end_date_formatted = isset($term->end_date) ? formatDate($term->end_date) : null;
        $term->activated_at_formatted = isset($term->activated_at) ? formatDate($term->activated_at) : null;
        $term->started_at_formatted = isset($term->started_at) ? formatDate($term->started_at) : null;
        $term->ended_at_formatted = isset($term->ended_at) ? formatDate($term->ended_at) : null;
        $term->created_at_formatted = isset($term->created_at) ? formatDate($term->created_at) : null;
        $term->updated_at_formatted = isset($term->updated_at) ? formatDate($term->updated_at) : null;

        return $term;
    }

    /**
     * Delete a term.
     *
     * @param int $id
     * @return void
     * @throws BusinessValidationException
     */
    public function deleteTerm($id): void
    {
        $term = AcademicTerm::findOrFail($id);
        if ($term->reservations()->count() > 0) {
            throw new BusinessValidationException(__('This academic term cannot be deleted because it has active reservations.'));
        }
        $term->delete();
    }

    /**
     * Set a term as active or inactive (start/end term)
     * @param int $id
     * @param bool $active
     * @return AcademicTerm
     * @throws BusinessValidationException
     */
    public function setActive($id, bool $active): AcademicTerm
    {
        $term = AcademicTerm::findOrFail($id);
        if ($active && $this->isOldYear($term)) {
            throw new BusinessValidationException(__('This academic term belongs to an old year and cannot be modified.'));
        }
        $term->active = $active;
        $term->activated_at = $active ? now() : null;
        $term->save();
        return $term->fresh();
    }

    /**
     * Start a term by activating all confirmed reservations for that term.
     *
     * @param int $id
     * @return AcademicTerm
     * @throws BusinessValidationException
     */
    public function start($id)
    {
        $term = AcademicTerm::findOrFail($id);
        if (!$term->active) {
            throw new BusinessValidationException(__('The selected academic term is not active.'));
        }

        if ($term->current) {
            throw new BusinessValidationException(__('The selected academic term is already set as the current term.'));
        }

        if ($this->isOldYear($term)) {
            throw new BusinessValidationException(__('The selected academic term belongs to a past year and cannot be modified.'));
        }

        $currentTerm = AcademicTerm::where('current', true)->first();
        if ($currentTerm && $currentTerm->id !== $term->id) {
            throw new BusinessValidationException(__("The academic term {$term->name} cannot be started because {$currentTerm->name} is currently active."));
        }
        $activatedCount = 0;
        DB::transaction(function () use ($term, &$activatedCount) {
            $activatedCount = $this->handleAcademicTermReservationActivation($term);
            $term->current = true;
            $term->started_at = now();
            $term->save();
        });
        $freshTerm = $term->fresh();
        $freshTerm->activated_reservations_count = $activatedCount;
        return $freshTerm;
    }

    /**
     * Activate all confirmed, inactive reservations for the given term.
     *
     * @param AcademicTerm $term
     * @return int Number of reservations activated
     */
    private function handleAcademicTermReservationActivation(AcademicTerm $term)
    {
        $users = User::whereHas('reservations', function ($query) use ($term) {
            $query->where('academic_term_id', $term->id)
                  ->where('status', 'confirmed')
                  ->where('active', false);
        })->with(['reservations' => function ($query) use ($term) {
            $query->where('academic_term_id', $term->id)
                  ->where('status', 'confirmed')
                  ->where('active', false);
        }])->get();

        if ($users->isEmpty()) {
            return 0;
        }

        $activatedCount = DB::table('reservations')
            ->where('academic_term_id', $term->id)
            ->where('status', 'confirmed')
            ->where('active', false)
            ->update([
                'status' => 'active',
                'active' => true,
                'activated_at' => now(),
            ]);

        Notification::send($users, (new ReservationActivated($term))->afterCommit());

        return $activatedCount;
    }

    /**
     * End a term by deactivating it and setting current to false.
     *
     * @param int $id
     * @return AcademicTerm
     * @throws BusinessValidationException
     */
    public function end($id): AcademicTerm
    {
        $term = AcademicTerm::findOrFail($id);
        if (!$term->current) {
            throw new BusinessValidationException('The selected academic term is not set as the current term.');
        }

        if ($term->reservations->count() > 0) {
            throw new BusinessValidationException('The selected academic term has existing reservations and cannot be modified.');
        }
        $term->current = false;
        $term->active = false;
        $term->ended_at = now();
        $term->save();
        return $term->fresh();
    }

    /**
     * Get all active terms (for dropdown and forms).
     *
     * @return array
     */
    public function getAll(): array
    {
        return AcademicTerm::orderBy('year', 'desc')
            ->orderBy('season')
            ->get()
            ->map(function ($term) {
                return [
                    'id' => $term->id,
                    'name' => $term->name,
                    'season' => $term->season,
                    'year' => $term->year,
                    'code' => $term->code,
                    'active' => (bool) $term->active,
                ];
            })
            ->toArray();
    }

    /**
     * Get all terms including inactive ones.
     *
     * @return array
     */
    public function getAllWithInactive(): array
    {
        return AcademicTerm::orderBy('year', 'desc')
            ->orderBy('season')
            ->get()
            ->map(function ($term) {
                return [
                    'id' => $term->id,
                    'name' => $term->name,
                    'season' => $term->season,
                    'year' => $term->year,
                    'code' => $term->code,
                    'active' => (bool) $term->active,
                ];
            })
            ->toArray();
    }

    /**
     * Get term statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        $stats = AcademicTerm::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN active = true THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN active = false THEN 1 ELSE 0 END) as inactive,
            MAX(CASE WHEN current = true THEN updated_at END) as current_last_update,
            MAX(CASE WHEN active = true THEN updated_at END) as active_last_update,
            MAX(CASE WHEN active = false THEN updated_at END) as inactive_last_update
        ')->first();

        $currentTerm = AcademicTerm::where('current', true)->first();

        return [
            'terms' => [
                'count' => formatNumber($stats->total),
                'lastUpdateTime' => formatDate($stats->current_last_update),
            ],
            'active' => [
                'count' => formatNumber($stats->active),
                'lastUpdateTime' => formatDate($stats->active_last_update),
            ],
            'inactive' => [
                'count' => formatNumber($stats->inactive),
                'lastUpdateTime' => formatDate($stats->inactive_last_update),
            ],
            'current' => [
                'title' => $currentTerm ? $currentTerm->name : __('No Set'),
                'lastUpdateTime' => $currentTerm ? formatDate($currentTerm->updated_at) : null,
            ],
        ];
    }


    /**
     * Get term data for DataTables.
     *
     * @return JsonResponse
     */
    public function getDatatable(): JsonResponse
    {
        $terms = AcademicTerm::withCount('reservations');
        $terms = $this->applySearchFilters($terms);
        return DataTables::of($terms)
            ->addIndexColumn()
            ->addColumn('season', fn($term) => __($term->season))
            ->addColumn('year', fn($term) => $term->year)
            ->addColumn('code', fn($term) => $term->code)
            ->addColumn('start_date', fn($term) => formatDate($term->start_date))
            ->addColumn('end_date', fn($term) => formatDate($term->end_date) ?: __('Not Set'))
            ->addColumn('reservations', fn($term) => formatNumber($term->reservations_count))
            ->addColumn('status', fn($term) => $this->renderStatusBadge($term))
            ->addColumn('action', fn($term) => $this->renderActionButtons($term))
            ->orderColumn('reservations', 'reservations_count $1')
            ->orderColumn('start_date', 'start_date $1')
            ->orderColumn('end_date', 'end_date $1')
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Apply search filters to the query.
     *
     * @param Builder $query
     * @return Builder
     */
    private function applySearchFilters($query): Builder
    {
        if (request()->filled('search_season')) {
            $query->where('season', request('search_season'));
        }
        if (request()->filled('search_year')) {
            $searchYear = request('search_year');
            if (strpos($searchYear, '-') !== false) {
                $years = explode('-', $searchYear);
                if (count($years) == 2) {
                    $query->where('year', $searchYear);
                }
            } else {
                $query->where('year', 'LIKE', '%' . $searchYear . '%');
            }
        }
        if (request()->filled('search_active')) {
            $query->where('active', request('search_active'));
        }
        return $query;
    }

    /**
     * Render status badge for datatable.
     *
     * @param AcademicTerm $term
     * @return string
     */
    private function renderStatusBadge(AcademicTerm $term): string
    {
        return $term->active
            ? '<span class="badge bg-label-success">' . __('active') . '</span>'
            : '<span class="badge bg-label-secondary">' . __('inactive') . '</span>';
    }

    /**
     * Render action buttons for datatable rows.
     *
     * @param AcademicTerm $term
     * @return string
     */
    private function renderActionButtons(AcademicTerm $term): string
    {
        $singleActions = [];
        if (!$term->current) {
            $singleActions[] = [
                'action' => $term->active ? 'deactivate' : 'activate',
                'icon' => $term->active ? 'bx bx-toggle-left' : 'bx bx-toggle-right',
                'class' => $term->active ? 'btn-warning' : 'btn-success',
                'label' => $term->active ? __('deactivate') : __('activate')
            ];
        }
        if ($term->active && !$term->current) {
            $singleActions[] = [
                'action' => 'start',
                'icon' => 'bx bx-play-circle',
                'class' => 'btn-success',
                'label' => __('start')
            ];
        }
        if ($term->current) {
            $singleActions[] = [
                'action' => 'end',
                'icon' => 'bx bx-stop-circle',
                'class' => 'btn-secondary',
                'label' => __('end')
            ];
        }
        return view('components.ui.datatable.table-actions', [
            'mode' => 'both',
            'actions' => ['view', 'edit', 'delete'],
            'id' => $term->id,
            'type' => 'Term',
            'singleActions' => $singleActions
        ])->render();
    }

    /**
     * Generate semester code based on season and academic year.
     *
     * @param string $season The season (fall, spring, summer)
     * @param string $academicYear The academic year in format "YYYY-YYYY" (e.g., "2021-2022")
     * @return string The generated semester code
     */
    private function generateCode($season, $academicYear): string
    {
        $season = strtolower(trim($season));
        switch ($season) {
            case 'fall':
                $seasonCode = '1';
                break;
            case 'spring':
                $seasonCode = '2';
                break;
            case 'summer':
                $seasonCode = '3';
                break;
            default:
                throw new BusinessValidationException(
                    __('Invalid value provided for :field.', ['field' => 'season'])
                );
        }
        $years = explode('-', $academicYear);
        $startYear = trim($years[0]);
        $shortYear = substr($startYear, -2);
        return $shortYear . $seasonCode;
    }

    /**
     * Determine if the given academic term belongs to a previous academic year.
     *
     * @param AcademicTerm $term
     * @return bool
     */
    private function isOldYear(AcademicTerm $term): bool
    {
        [$startYear, $endYear] = array_map('intval', explode('-', $term->year));
        $currentYear = now()->year;
        return $endYear < $currentYear;
    }
}