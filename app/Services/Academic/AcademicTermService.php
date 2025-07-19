<?php

namespace App\Services\Academic;

use App\Models\AcademicTerm;
use App\Batches\AcademicTermReservationActivationBatch;
use App\Exceptions\BusinessValidationException;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;

class AcademicTermService
{
    /**
     * The model class to use for this service.
     * @var string
     */
    protected $model = AcademicTerm::class;

    /**
     * Create a new term.
     *
     * @param array $data
     * @return AcademicTerm
     */
    public function createTerm(array $data): AcademicTerm
    {
        $code = $this->generateCode($data['season'], $data['year']);
        
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
     * @param AcademicTerm $term
     * @param array $data
     * @return AcademicTerm
     */
    public function updateTerm(AcademicTerm $term, array $data): AcademicTerm
    {
        $code = $this->generateCode($data['season'], $data['year']);
        
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
     * Get a single term with its reservations.
     *
     * @param int $id
     * @return AcademicTerm
     */
    public function getTerm($id): AcademicTerm
    {
        return AcademicTerm::with('reservations')->findOrFail($id);
    }

    /**
     * Delete a term.
     *
     * @param AcademicTerm $term
     * @return void
     * @throws BusinessValidationException
     */
    public function deleteTerm(AcademicTerm $term): void
    {
        if ($term->reservations()->count() > 0) {
            throw new BusinessValidationException('Cannot delete term that has reservations assigned.');
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
            throw new BusinessValidationException('Cannot activate terms from previous academic years.');
        }
        
        $term->active = $active;
        $term->save();
        return $term->fresh();
    }

    /**
     * Start a term by activating all confirmed reservations for that term.
     *
     * @param int $id
     * @return \Illuminate\Bus\Batch
     * @throws BusinessValidationException
     */
    public function start($id)
    {
        $term = AcademicTerm::findOrFail($id);

        if (!$term->active) {
            throw new BusinessValidationException('Term must be active before it can be started.');
        }

        if ($term->current) {
            throw new BusinessValidationException('Term is already current.');
        }

        if ($this->isOldYear($term)) {
            throw new BusinessValidationException('Cannot start terms from previous academic years.');
        }

        $batch = AcademicTermReservationActivationBatch::create($term);

        return $batch;
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
            throw new BusinessValidationException('Term is not currently active.');
        }

        if ($term->reservations->count() > 0) {
            throw new BusinessValidationException('Cannot end term while there are active reservations.');
        }

        $term->current = false;
        $term->active = false;

        $term->save();

        return $term->fresh();
    }

    /**
     * Check if a term is from a previous academic year.
     *
     * @param AcademicTerm $term
     * @return bool
     */
    /**
     * Determine if the given academic term belongs to a previous academic year.
     *
     * @param AcademicTerm $term
     * @return bool
     */
    private function isOldYear(AcademicTerm $term): bool
    {
        // Parse the start and end years from the academic year string (e.g., "2023-2024" => 2023, 2024)
        [$startYear, $endYear] = array_map('intval', explode('-', $term->year));

        $now = now();
        $currentYear = $now->year;
        $currentMonth = $now->month;

        // Otherwise, it's old if both years are before the current academic year
        return $endYear < $currentAcademicYearStart;
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
        $totalTerms = AcademicTerm::count();
        $activeTerms = AcademicTerm::where('active', true)->count();
        $inactiveTerms = AcademicTerm::where('active', false)->count();
        $currentTerm = AcademicTerm::where('current', true)->first();
        $lastUpdateTime = formatDate(AcademicTerm::max('updated_at'));

        return [
            'total' => [
                'total' => formatNumber($totalTerms),
                'lastUpdateTime' => $lastUpdateTime
            ],
            'active' => [
                'total' => formatNumber($activeTerms),
                'lastUpdateTime' => $lastUpdateTime
            ],
            'inactive' => [
                'total' => formatNumber($inactiveTerms),
                'lastUpdateTime' => $lastUpdateTime
            ],
            'current' => [
                'total' => $currentTerm ? $currentTerm->name : 'No Current Term',
                'lastUpdateTime' => $currentTerm ? formatDate($currentTerm->updated_at) : $lastUpdateTime
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
            ->addColumn('name', fn($term) => $term->name)
            ->addColumn('start_date', fn($term) => formatDate($term->start_date))
            ->addColumn('end_date', fn($term) => formatDate($term->end_date))
            ->addColumn('reservations', fn($term) => formatNumber($term->reservations_count))
            ->addColumn('status', fn($term) => $this->renderStatusBadge($term))
            ->addColumn('action', fn($term) => $this->renderActionButtons($term))
            ->orderColumn('reservations', 'reservations_count $1')
            ->orderColumn('start_date', 'start_date $1')
            ->orderColumn('end_date', 'end_date $1')
            ->rawColumns(['status', 'current', 'action'])
            ->make(true);
    }

    /**
     * Apply search filters to the query.
     *
     * @param Builder $query
     * @return Builder
     */
    protected function applySearchFilters($query): Builder
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

        if (request()->filled('search_code')) {
            $query->where('code', 'LIKE', '%' . request('search_code') . '%');
        }

        if (request()->filled('search_active')) {
            $query->where('active', request('search_active'));
        }

        return $query;
    }

    /**
     * Generate semester code based on season and academic year.
     *
     * @param string $season The season (fall, spring, summer)
     * @param string $academicYear The academic year in format "YYYY-YYYY" (e.g., "2021-2022")
     * @return string The generated semester code
     */
    public function generateCode($season, $academicYear): string
    {
        $season = strtolower(trim($season));
        
        // Map seasons to their codes
        $seasonCode = match($season) {
            'fall' => '1',
            'spring' => '2', 
            'summer' => '3',
        };
        
        // Extract the starting year from academic year string
        $years = explode('-', $academicYear);
        
        $startYear = trim($years[0]);
        
        // Get last 2 digits of the starting year
        $shortYear = substr($startYear, -2);
        
        return $shortYear . $shortYear . $seasonCode;
    }

    /**
     * Render status badge for datatable.
     *
     * @param AcademicTerm $term
     * @return string
     */
    public function renderStatusBadge(AcademicTerm $term): string
    {
        return $term->active 
            ? '<span class="badge bg-label-success">Active</span>'
            : '<span class="badge bg-label-secondary">Inactive</span>';
    }


    /**
     * Render action buttons for datatable rows.
     *
     * @param AcademicTerm $term
     * @return string
     */
    public function renderActionButtons(AcademicTerm $term): string
    {
        $singleActions = [];

        // Only show activate/deactivate button if term is NOT current
        if (!$term->current) {
            $singleActions[] = [
                'action' => $term->active ? 'deactivate' : 'activate',
                'icon' => $term->active ? 'bx bx-toggle-left' : 'bx bx-toggle-right',
                'class' => $term->active ? 'btn-warning' : 'btn-success',
                'label' => $term->active ? 'Deactivate' : 'Activate'
            ];
        }

        // Only show start button if term is active but not current
        if ($term->active && !$term->current) {
            $singleActions[] = [
                'action' => 'start',
                'icon' => 'bx bx-play-circle',
                'class' => 'btn-success',
                'label' => 'Start Term'
            ];
        }

        // Only show end button if term is current
        if ($term->current) {
            $singleActions[] = [
                'action' => 'end',
                'icon' => 'bx bx-stop-circle',
                'class' => 'btn-secondary',
                'label' => 'End Term'
            ];
        }

        return view('components.ui.datatable.data-table-actions', [
            'mode' => 'both',
            'actions' => ['edit', 'delete'],
            'id' => $term->id,
            'type' => 'Term',
            'singleActions' => $singleActions
        ])->render();
    }
}