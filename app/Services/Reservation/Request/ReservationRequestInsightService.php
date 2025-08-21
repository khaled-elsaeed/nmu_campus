<?php

namespace App\Services\Reservation\Request;

use Illuminate\Http\Request;
use App\Models\Reservation\ReservationRequest;
use App\Models\Governorate;
use App\Models\Academic\Faculty;
use App\Models\Academic\Program;
use Illuminate\Database\Eloquent\Builder;

class ReservationRequestInsightService
{
    /**
     * Apply common filters to the query
     */
    private function applyFilters(Builder $query, Request $request)
    {
        \Log::info('applyFilters called with', [
            'filled_academic_term' => $request->filled('filter_academic_term'),
            'academic_term_value' => $request->get('filter_academic_term'),
            'filled_status' => $request->filled('filter_status'),  
            'status_value' => $request->get('filter_status'),
            'filled_date_from' => $request->filled('filter_date_from'),
            'date_from_value' => $request->get('filter_date_from'),
            'filled_date_to' => $request->filled('filter_date_to'),
            'date_to_value' => $request->get('filter_date_to'),
            'filled_gender' => $request->filled('gender'),
            'gender_value' => $request->get('gender'),
            'filled_faculty' => $request->filled('faculty'),
            'faculty_value' => $request->get('faculty'),
            'filled_governorate' => $request->filled('governorate'),
            'governorate_value' => $request->get('governorate'),
        ]);

        // Filter by academic term
        if ($request->filled('filter_academic_term')) {
            \Log::info('Applying academic_term filter', ['value' => $request->filter_academic_term]);
            $query->where('reservation_requests.academic_term_id', $request->filter_academic_term);
        }

        // Filter by status
        if ($request->filled('filter_status')) {
            \Log::info('Applying status filter', ['value' => $request->filter_status]);
            $query->where('reservation_requests.status', $request->filter_status);
        }

        // Filter by date range
        if ($request->filled('filter_date_from')) {
            \Log::info('Applying date_from filter', ['value' => $request->filter_date_from]);
            $query->whereDate('reservation_requests.check_in_date', '>=', $request->filter_date_from);
        }

        if ($request->filled('filter_date_to')) {
            \Log::info('Applying date_to filter', ['value' => $request->filter_date_to]);
            $query->whereDate('reservation_requests.check_in_date', '<=', $request->filter_date_to);
        }

        // Additional common filters using whereHas (this works before joins)
        if ($request->filled('gender')) {
            \Log::info('Applying gender filter with whereHas', ['value' => $request->get('gender')]);
            $query->whereHas('user', function($q) use ($request) {
                $q->where('gender', $request->get('gender'));
            });
        }

        if ($request->filled('faculty')) {
            \Log::info('Applying faculty filter with whereHas', ['value' => $request->get('faculty')]);
            $query->whereHas('user.student', function($q) use ($request) {
                $q->where('faculty_id', $request->get('faculty'));
            });
        }

        if ($request->filled('governorate')) {
            \Log::info('Applying governorate filter with whereHas', ['value' => $request->get('governorate')]);
            $query->whereHas('user.student', function($q) use ($request) {
                $q->where('governorate_id', $request->get('governorate'));
            });
        }

        return $query;
    }

    /**
     * Apply filters when users table is already joined
     */
    private function applyFiltersWithUserJoin(Builder $query, Request $request)
    {
        // Filter by academic term
        if ($request->filled('filter_academic_term')) {
            $query->where('reservation_requests.academic_term_id', $request->filter_academic_term);
        }

        // Filter by status
        if ($request->filled('filter_status')) {
            $query->where('reservation_requests.status', $request->filter_status);
        }

        // Filter by date range
        if ($request->filled('filter_date_from')) {
            $query->whereDate('reservation_requests.check_in_date', '>=', $request->filter_date_from);
        }

        if ($request->filled('filter_date_to')) {
            $query->whereDate('reservation_requests.check_in_date', '<=', $request->filter_date_to);
        }

        // Filter by gender (users table already joined)
        if ($request->filled('filter_gender')) {
            $query->where('users.gender', $request->filter_gender);
        }

        return $query;
    }

    /**
     * Apply filters when users and students tables are already joined
     */
    private function applyFiltersWithStudentJoin(Builder $query, Request $request)
    {
        // Apply base filters
        $query = $this->applyFiltersWithUserJoin($query, $request);

        // Filter by faculty (students table already joined)
        if ($request->filled('filter_faculty')) {
            $query->where('students.faculty_id', $request->filter_faculty);
        }

        // Filter by governorate (students table already joined)
        if ($request->filled('filter_governorate')) {
            $query->where('students.governorate_id', $request->filter_governorate);
        }

        return $query;
    }

    public function getAnalyticsStats(Request $request)
    {
        $query = ReservationRequest::leftJoin('users', 'reservation_requests.user_id', '=', 'users.id');
        
        // Apply filters
        $query = $this->applyFilters($query, $request);

        $stats = $query->selectRaw("
                COUNT(*) as total,
                MAX(reservation_requests.updated_at) as last_update,

                SUM(CASE WHEN reservation_requests.status = 'pending' THEN 1 ELSE 0 END) as pending,
                MAX(CASE WHEN reservation_requests.status = 'pending' THEN reservation_requests.updated_at ELSE NULL END) as pending_last_update,
                SUM(CASE WHEN reservation_requests.status = 'pending' AND users.gender = 'male' THEN 1 ELSE 0 END) as pending_male,
                SUM(CASE WHEN reservation_requests.status = 'pending' AND users.gender = 'female' THEN 1 ELSE 0 END) as pending_female,

                SUM(CASE WHEN reservation_requests.status = 'approved' THEN 1 ELSE 0 END) as approved,
                MAX(CASE WHEN reservation_requests.status = 'approved' THEN reservation_requests.updated_at ELSE NULL END) as approved_last_update,
                SUM(CASE WHEN reservation_requests.status = 'approved' AND users.gender = 'male' THEN 1 ELSE 0 END) as approved_male,
                SUM(CASE WHEN reservation_requests.status = 'approved' AND users.gender = 'female' THEN 1 ELSE 0 END) as approved_female,

                SUM(CASE WHEN reservation_requests.status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                MAX(CASE WHEN reservation_requests.status = 'rejected' THEN reservation_requests.updated_at ELSE NULL END) as rejected_last_update,
                SUM(CASE WHEN reservation_requests.status = 'rejected' AND users.gender = 'male' THEN 1 ELSE 0 END) as rejected_male,
                SUM(CASE WHEN reservation_requests.status = 'rejected' AND users.gender = 'female' THEN 1 ELSE 0 END) as rejected_female,

                SUM(CASE WHEN reservation_requests.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                MAX(CASE WHEN reservation_requests.status = 'cancelled' THEN reservation_requests.updated_at ELSE NULL END) as cancelled_last_update,
                SUM(CASE WHEN reservation_requests.status = 'cancelled' AND users.gender = 'male' THEN 1 ELSE 0 END) as cancelled_male,
                SUM(CASE WHEN reservation_requests.status = 'cancelled' AND users.gender = 'female' THEN 1 ELSE 0 END) as cancelled_female,

                SUM(CASE WHEN users.gender = 'male' THEN 1 ELSE 0 END) as total_male,
                SUM(CASE WHEN users.gender = 'female' THEN 1 ELSE 0 END) as total_female
            ")
            ->first();

        return [
            'reservation-requests' => [
                'count' => formatNumber($stats->total),
                'male' => formatNumber($stats->total_male),
                'female' => formatNumber($stats->total_female),
                'lastUpdateTime' => formatDate($stats->last_update),
            ],
            'reservation-requests-pending' => [
                'count' => formatNumber($stats->pending),
                'male' => formatNumber($stats->pending_male),
                'female' => formatNumber($stats->pending_female),
                'lastUpdateTime' => formatDate($stats->pending_last_update),
            ],
            'reservation-requests-approved' => [
                'count' => formatNumber($stats->approved),
                'male' => formatNumber($stats->approved_male),
                'female' => formatNumber($stats->approved_female),
                'lastUpdateTime' => formatDate($stats->approved_last_update),
            ],
            'reservation-requests-rejected' => [
                'count' => formatNumber($stats->rejected),
                'male' => formatNumber($stats->rejected_male),
                'female' => formatNumber($stats->rejected_female),
                'lastUpdateTime' => formatDate($stats->rejected_last_update),
            ],
            'reservation-requests-cancelled' => [
                'count' => formatNumber($stats->cancelled),
                'male' => formatNumber($stats->cancelled_male),
                'female' => formatNumber($stats->cancelled_female),
                'lastUpdateTime' => formatDate($stats->cancelled_last_update),
            ],
        ];
    }

    public function getAnalyticsRoomTypes(Request $request)
    {
        $query = ReservationRequest::query();
        
        // Apply filters
        $query = $this->applyFilters($query, $request);

        $roomTypes = $query->selectRaw("
            SUM(CASE WHEN accommodation_type = 'room' AND room_type = 'single' THEN 1 ELSE 0 END) as single,
            SUM(CASE WHEN accommodation_type = 'room' AND room_type = 'double' THEN 1 ELSE 0 END) as double_total,
            SUM(CASE WHEN accommodation_type = 'room' AND room_type = 'double' AND bed_count = 1 THEN 1 ELSE 0 END) as double_single_bed,
            SUM(CASE WHEN accommodation_type = 'room' AND room_type = 'double' AND bed_count = 2 THEN 1 ELSE 0 END) as double_double_bed
        ")->first();

        return [
            ['room_type' => 'Single', 'count' => formatNumber($roomTypes->single)],
            ['room_type' => 'Double Total', 'count' => formatNumber($roomTypes->double_total)],
            ['room_type' => 'Double Single Bed', 'count' => formatNumber($roomTypes->double_single_bed)],
            ['room_type' => 'Double Double Bed', 'count' => formatNumber($roomTypes->double_double_bed)],
        ];
    }

    public function getAnalyticsParentAbroad(Request $request)
    {
        $query = ReservationRequest::join('users', 'users.id', '=', 'reservation_requests.user_id')
            ->join('parents', 'parents.user_id', '=', 'users.id');
            
        // Apply filters
        $query = $this->applyFilters($query, $request);

        $analytics = $query->selectRaw('
                SUM(CASE WHEN parents.is_abroad = 1 THEN 1 ELSE 0 END) as abroad_count,
                SUM(CASE WHEN parents.is_abroad = 0 THEN 1 ELSE 0 END) as local_count
            ')
            ->first();

        return [
            ['parent_abroad' => true, 'count' => formatNumber($analytics->abroad_count)],
            ['parent_abroad' => false, 'count' => formatNumber($analytics->local_count)],
        ];
    }

    public function getAnalyticsGovernorates(Request $request)
    {
        $query = ReservationRequest::join('users', 'users.id', '=', 'reservation_requests.user_id')
            ->join('students', 'users.id', '=', 'students.user_id')
            ->whereNotNull('students.governorate_id');
            
        // Apply filters
        $query = $this->applyFilters($query, $request);

        $counts = $query->selectRaw('students.governorate_id as id, COUNT(*) as count')
            ->groupBy('students.governorate_id')
            ->pluck('count', 'id');

        $governorates = Governorate::whereIn('id', $counts->keys())->get();

        return $governorates->map(fn($gov) => [
            'governorate' => $gov->name ?? 'Unknown',
            'count' => formatNumber($counts[$gov->id]),
        ])->values();
    }

    public function getAnalyticsFaculties(Request $request)
    {
        $query = ReservationRequest::join('users', 'users.id', '=', 'reservation_requests.user_id')
            ->join('students', 'users.id', '=', 'students.user_id')
            ->whereNotNull('students.faculty_id');
            
        // Apply filters
        $query = $this->applyFilters($query, $request);

        $counts = $query->selectRaw('students.faculty_id as id, COUNT(*) as count')
            ->groupBy('students.faculty_id')
            ->pluck('count', 'id');

        $faculties = Faculty::whereIn('id', $counts->keys())->get();

        return $faculties->map(fn($f) => [
            'faculty' => $f->name ?? 'Unknown',
            'count' => (int) $counts[$f->id],
        ])->values();
    }

    public function getAnalyticsPrograms(Request $request)
    {
        $facultyId = $request->get('faculty_id');

        $query = ReservationRequest::join('users', 'users.id', '=', 'reservation_requests.user_id')
            ->join('students', 'users.id', '=', 'students.user_id')
            ->whereNotNull('students.program_id');

        // Apply filters
        $query = $this->applyFilters($query, $request);

        if ($facultyId) {
            $query->where('students.faculty_id', $facultyId);
        }

        $counts = $query->selectRaw('students.program_id as id, COUNT(*) as count')
            ->groupBy('students.program_id')
            ->pluck('count', 'id');

        $programs = Program::whereIn('id', $counts->keys())->get();

        return $programs->map(fn($p) => [
            'program' => $p->name ?? 'Unknown',
            'count' => formatNumber($counts[$p->id]),
        ])->values();
    }

    public function getAnalyticsGenders(Request $request)
    {
        $query = ReservationRequest::join('users', 'users.id', '=', 'reservation_requests.user_id');
        
        // Apply filters
        $query = $this->applyFilters($query, $request);

        $analytics = $query->selectRaw('users.gender, COUNT(*) as count')
            ->groupBy('users.gender')
            ->get()
            ->map(fn($item) => [
                'gender' => $item->gender ?? 'unknown',
                'count' => formatNumber($item->count),
            ]);

        return $analytics;
    }

    public function getAnalyticsSiblingPreferences(Request $request)
    {
        $query = ReservationRequest::query();
        
        // Apply filters
        $query = $this->applyFilters($query, $request);

        $analytics = $query->selectRaw('reservation_requests.stay_with_sibling, COUNT(*) as count')
            ->groupBy('reservation_requests.stay_with_sibling')
            ->get()
            ->map(fn($item) => [
                'stay_with_sibling' => (int) $item->stay_with_sibling,
                'count' => formatNumber($item->count),
            ]);

        return $analytics;
    }


}