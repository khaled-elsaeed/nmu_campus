<?php

namespace App\Http\Controllers\Reservation;

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;
use App\Services\Reservation\Request\ReservationRequestService;
use App\Exceptions\BusinessValidationException;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\Request\UpdateReservationRequest;
use Illuminate\Support\Facades\DB;

class ReservationRequestController extends Controller
{
    /**
     * ReservationRequestController constructor.
     *
     * @param ReservationRequestService $reservationRequestService
     */
    public function __construct(protected ReservationRequestService $reservationRequestService)
    {}

    /**
     * Display the reservation management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('reservation.request.index');
    }

    public function insights(): View
    {
        return view('reservation.request.insights');
    }

    /**
     * Get reservation statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->reservationRequestService->getStats();
            return successResponse('Stats fetched successfully.', $stats);
        } catch (Exception $e) {
            logError('ReservationRequestController@stats', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Get reservation data for DataTables.
     *
     * @return JsonResponse
     */
    public function datatable(): JsonResponse
    {
        try {
            return $this->reservationRequestService->getDatatable();
        } catch (Exception $e) {
            logError('ReservationRequestController@datatable', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Display the specified reservation.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $reservation = $this->reservationRequestService->show($id);
            if (!$reservation) {
                return errorResponse('Reservation not found.', [], 404);
            }
            return successResponse('Reservation fetched successfully.', $reservation);
        } catch (Exception $e) {
            logError('ReservationRequestController@show', $e, ['id' => $id]);
            return errorResponse('Failed to fetch reservation.', [$e->getMessage()]);
        }
    }


    /**
     * Update the specified reservation.
     *
     * @param UpdateReservationRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateReservationRequest $request, $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $reservation = $this->reservationRequestService->getReservation($id);
            $updatedReservation = $this->reservationRequestService->updateReservation($reservation, $validated);
            return successResponse('Reservation updated successfully.', $updatedReservation);
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('ReservationRequestController@update', $e, ['id' => $id, 'request' => $request->all()]);
            return errorResponse('Failed to update reservation.', [$e->getMessage()]);
        }
    }

    public function accept(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate(
                [   'accommodation_type' => 'required|string|in:room,apartment',
                    'accommodation_id' => [
                        'required',
                        'integer',
                        function ($attribute, $value, $fail) use ($request) {
                            $type = $request->input('accommodation_type');

                            if ($type === 'room') {
                                if (!DB::table('rooms')->where('id', $value)->exists()) {
                                    return $fail(__('The selected room does not exist.'));
                                }
                            } elseif ($type === 'apartment') {
                                if (!DB::table('apartments')->where('id', $value)->exists()) {
                                    return $fail(__('The selected apartment does not exist.'));
                                }
                            } else {
                                return $fail(__('Invalid accommodation type.'));
                            }
                        },
                    ],
                    'bed_count' => 'nullable|string|max:255',
                    'notes' => 'nullable|string|max:1000',

                ]
            );
            $this->reservationRequestService->acceptRequest($validated, $id);
            return successResponse('Reservation request accepted successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('ReservationRequestController@accept', $e, ['id' => $id]);
            return errorResponse('Failed to accept reservation request.', [$e->getMessage()]);
        }
    }

    public function cancel($id): JsonResponse
    {
        try {
            $this->reservationRequestService->cancelRequest($id);
            return successResponse('Reservation request canceled successfully.');
        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage(), [], 422);
        } catch (Exception $e) {
            logError('ReservationRequestController@cancel', $e, ['id' => $id]);
            return errorResponse('Failed to cancel reservation request.', [$e->getMessage()]);
        }
    }

    /**
 * Get analytics overview data
 */
public function analyticsStats(Request $request)
{
    // Mock data for overview statistics
    $data = [
        'reservation_requests' => 450,
        'reservation_requests_pending' => 85,
        'reservation_requests_approved' => 320,
        'reservation_requests_rejected' => 45,
        'reservation_requests_cancelled' => 30
    ];

    return response()->json(['success' => true, 'data' => $data]);
}

/**
 * Get accommodation types analytics
 */
public function analyticsAccommodationTypes(Request $request)
{
    $data = [
        ['accommodation_type' => 'room', 'count' => 280],
        ['accommodation_type' => 'apartment', 'count' => 170]
    ];

    return response()->json(['success' => true, 'data' => $data]);
}

/**
 * Get room types analytics
 */
public function analyticsRoomTypes(Request $request)
{

    $data = [
        ['room_type' => 'single', 'count' => 80],
        ['room_type' => 'double', 'count' => 120],
        ['room_type' => 'single_bed', 'count' => 90],
        ['room_type' => 'double_bed', 'count' => 100],
        ['room_type' => 'both_bed', 'count' => 60]
    ];
    return response()->json(['success' => true, 'data' => $data]);
}

/**
 * Get parent abroad analytics
 */
public function analyticsParentAbroad(Request $request)
{

    $data = [
        ['parent_abroad' => 1, 'count' => 70], // Yes
        ['parent_abroad' => 0, 'count' => 380] // No
    ];
    return response()->json(['success' => true, 'data' => $data]);
}

/**
 * Get governorates analytics
 */
public function analyticsGovernorates(Request $request)
{
    $data = [
        ['governorate' => 'North Governorate', 'count' => 120],
        ['governorate' => 'South Governorate', 'count' => 95],
        ['governorate' => 'East Governorate', 'count' => 75],
        ['governorate' => 'West Governorate', 'count' => 85],
        ['governorate' => 'Central Governorate', 'count' => 45],
        ['governorate' => 'Capital Governorate', 'count' => 30]
    ];

    return response()->json(['success' => true, 'data' => $data]);
}

/**
 * Get faculties analytics
 */
public function analyticsFaculties(Request $request)
{
    $data = [
        ['faculty_id' => 1, 'faculty_name' => 'Engineering', 'count' => 120],
        ['faculty_id' => 2, 'faculty_name' => 'Medicine', 'count' => 95],
        ['faculty_id' => 3, 'faculty_name' => 'Business', 'count' => 75],
        ['faculty_id' => 4, 'faculty_name' => 'Arts & Sciences', 'count' => 85],
        ['faculty_id' => 5, 'faculty_name' => 'Law', 'count' => 45],
        ['faculty_id' => 6, 'faculty_name' => 'Pharmacy', 'count' => 30]
    ];

    return response()->json(['success' => true, 'data' => $data]);
}

/**
 * Get programs analytics
 */
public function analyticsPrograms(Request $request)
{
    $facultyId = $request->get('faculty_id');
    
    $programsData = [
        1 => [ // Engineering
            ['program_name' => 'Computer Engineering', 'count' => 45, 'approved' => 35, 'pending' => 8, 'rejected' => 2],
            ['program_name' => 'Electrical Engineering', 'count' => 35, 'approved' => 28, 'pending' => 5, 'rejected' => 2],
            ['program_name' => 'Civil Engineering', 'count' => 25, 'approved' => 20, 'pending' => 3, 'rejected' => 2],
            ['program_name' => 'Mechanical Engineering', 'count' => 15, 'approved' => 12, 'pending' => 2, 'rejected' => 1]
        ],
        2 => [ // Medicine
            ['program_name' => 'General Medicine', 'count' => 55, 'approved' => 45, 'pending' => 8, 'rejected' => 2],
            ['program_name' => 'Dentistry', 'count' => 25, 'approved' => 20, 'pending' => 3, 'rejected' => 2],
            ['program_name' => 'Pharmacy', 'count' => 15, 'approved' => 12, 'pending' => 2, 'rejected' => 1]
        ],
        3 => [ // Business
            ['program_name' => 'Business Administration', 'count' => 35, 'approved' => 28, 'pending' => 5, 'rejected' => 2],
            ['program_name' => 'Accounting', 'count' => 25, 'approved' => 20, 'pending' => 3, 'rejected' => 2],
            ['program_name' => 'Economics', 'count' => 15, 'approved' => 12, 'pending' => 2, 'rejected' => 1]
        ],
        4 => [ // Arts & Sciences
            ['program_name' => 'English Literature', 'count' => 30, 'approved' => 24, 'pending' => 4, 'rejected' => 2],
            ['program_name' => 'Mathematics', 'count' => 25, 'approved' => 20, 'pending' => 3, 'rejected' => 2],
            ['program_name' => 'Biology', 'count' => 20, 'approved' => 16, 'pending' => 3, 'rejected' => 1],
            ['program_name' => 'Chemistry', 'count' => 10, 'approved' => 8, 'pending' => 1, 'rejected' => 1]
        ],
        5 => [ // Law
            ['program_name' => 'Bachelor of Laws', 'count' => 35, 'approved' => 28, 'pending' => 5, 'rejected' => 2],
            ['program_name' => 'Master of Laws', 'count' => 10, 'approved' => 8, 'pending' => 1, 'rejected' => 1]
        ],
        6 => [ // Pharmacy
            ['program_name' => 'Clinical Pharmacy', 'count' => 20, 'approved' => 16, 'pending' => 3, 'rejected' => 1],
            ['program_name' => 'Pharmaceutical Sciences', 'count' => 10, 'approved' => 8, 'pending' => 1, 'rejected' => 1]
        ]
    ];

    $data = $programsData[$facultyId] ?? [];

    return response()->json(['success' => true, 'data' => $data]);
}

/**
 * Get genders analytics
 */
public function analyticsGenders(Request $request)
{
    $data = [
        ['gender' => 'male', 'count' => 245],
        ['gender' => 'female', 'count' => 205]
    ];

    return response()->json(['success' => true, 'data' => $data]);
}

/**
 * Get monthly trends analytics
 */
public function analyticsMonthlyTrends(Request $request)
{
    $data = [
        ['month' => 'Jan', 'month_name' => 'January', 'count' => 35],
        ['month' => 'Feb', 'month_name' => 'February', 'count' => 42],
        ['month' => 'Mar', 'month_name' => 'March', 'count' => 28],
        ['month' => 'Apr', 'month_name' => 'April', 'count' => 55],
        ['month' => 'May', 'month_name' => 'May', 'count' => 65],
        ['month' => 'Jun', 'month_name' => 'June', 'count' => 48],
        ['month' => 'Jul', 'month_name' => 'July', 'count' => 38],
        ['month' => 'Aug', 'month_name' => 'August', 'count' => 72],
        ['month' => 'Sep', 'month_name' => 'September', 'count' => 85],
        ['month' => 'Oct', 'month_name' => 'October', 'count' => 45],
        ['month' => 'Nov', 'month_name' => 'November', 'count' => 25],
        ['month' => 'Dec', 'month_name' => 'December', 'count' => 12]
    ];

    return response()->json(['success' => true, 'data' => $data]);
}

/**
 * Get sibling preferences analytics
 */
public function analyticsSiblingPreferences(Request $request)
{
    $data = [
        ['stay_with_sibling' => 1, 'count' => 125],
        ['stay_with_sibling' => 0, 'count' => 325]
    ];

    return response()->json(['success' => true, 'data' => $data]);
}

/**
 * Get status timeline analytics
 */
public function analyticsStatusTimeline(Request $request)
{
    $data = [
        ['month' => 'Jan', 'status' => 'pending', 'count' => 8],
        ['month' => 'Jan', 'status' => 'confirmed', 'count' => 25],
        ['month' => 'Jan', 'status' => 'rejected', 'count' => 2],
        ['month' => 'Feb', 'status' => 'pending', 'count' => 12],
        ['month' => 'Feb', 'status' => 'confirmed', 'count' => 28],
        ['month' => 'Feb', 'status' => 'rejected', 'count' => 2],
        ['month' => 'Mar', 'status' => 'pending', 'count' => 6],
        ['month' => 'Mar', 'status' => 'confirmed', 'count' => 20],
        ['month' => 'Mar', 'status' => 'rejected', 'count' => 2],
        ['month' => 'Apr', 'status' => 'pending', 'count' => 15],
        ['month' => 'Apr', 'status' => 'confirmed', 'count' => 38],
        ['month' => 'Apr', 'status' => 'rejected', 'count' => 2],
        ['month' => 'May', 'status' => 'pending', 'count' => 18],
        ['month' => 'May', 'status' => 'confirmed', 'count' => 45],
        ['month' => 'May', 'status' => 'rejected', 'count' => 2],
        ['month' => 'Jun', 'status' => 'pending', 'count' => 12],
        ['month' => 'Jun', 'status' => 'confirmed', 'count' => 34],
        ['month' => 'Jun', 'status' => 'rejected', 'count' => 2]
    ];

    return response()->json(['success' => true, 'data' => $data]);
}

/**
 * Get period types analytics
 */
public function analyticsPeriodTypes(Request $request)
{
    $data = [
        ['period_type' => 'semester', 'count' => 285],
        ['period_type' => 'year', 'count' => 135],
        ['period_type' => 'summer', 'count' => 30]
    ];

    return response()->json(['success' => true, 'data' => $data]);
}

/**
 * Get summary statistics
 */
public function analyticsSummaryStats(Request $request)
{
    $data = [
        [
            'category' => 'Accommodation Types',
            'subcategory' => 'Rooms',
            'count' => 280,
            'percentage' => '62.2',
            'approved' => 210,
            'pending' => 50,
            'rejected' => 20
        ],
        [
            'category' => 'Accommodation Types',
            'subcategory' => 'Apartments',
            'count' => 170,
            'percentage' => '37.8',
            'approved' => 130,
            'pending' => 30,
            'rejected' => 10
        ],
        [
            'category' => 'Room Types',
            'subcategory' => 'Single',
            'count' => 180,
            'percentage' => '40.0',
            'approved' => 135,
            'pending' => 35,
            'rejected' => 10
        ],
        [
            'category' => 'Room Types',
            'subcategory' => 'Double',
            'count' => 270,
            'percentage' => '60.0',
            'approved' => 205,
            'pending' => 50,
            'rejected' => 15
        ],
        [
            'category' => 'Gender',
            'subcategory' => 'Male',
            'count' => 245,
            'percentage' => '54.4',
            'approved' => 185,
            'pending' => 45,
            'rejected' => 15
        ],
        [
            'category' => 'Gender',
            'subcategory' => 'Female',
            'count' => 205,
            'percentage' => '45.6',
            'approved' => 155,
            'pending' => 40,
            'rejected' => 10
        ],
        [
            'category' => 'Sibling Preference',
            'subcategory' => 'With Siblings',
            'count' => 125,
            'percentage' => '27.8',
            'approved' => 95,
            'pending' => 25,
            'rejected' => 5
        ],
        [
            'category' => 'Sibling Preference',
            'subcategory' => 'Individual',
            'count' => 325,
            'percentage' => '72.2',
            'approved' => 245,
            'pending' => 60,
            'rejected' => 20
        ]
    ];

    return response()->json(['success' => true, 'data' => $data]);
}

/**
 * Export analytics data
 */
public function analyticsExport(Request $request)
{
    $data = [
        'export_date' => now()->toDateTimeString(),
        'filters' => $request->all(),
        'summary' => [
            'total_requests' => 450,
            'pending' => 85,
            'approved' => 320,
            'rejected' => 45
        ],
        'details' => [
            'accommodation_types' => [
                ['type' => 'room', 'count' => 280],
                ['type' => 'apartment', 'count' => 170]
            ],
            'room_types' => [
                ['type' => 'single', 'count' => 180],
                ['type' => 'double', 'count' => 270]
            ],
            'faculties' => [
                ['name' => 'Engineering', 'count' => 120],
                ['name' => 'Medicine', 'count' => 95],
                ['name' => 'Business', 'count' => 75],
                ['name' => 'Arts & Sciences', 'count' => 85],
                ['name' => 'Law', 'count' => 45],
                ['name' => 'Pharmacy', 'count' => 30]
            ]
        ]
    ];

    return response()->json(['success' => true, 'data' => $data]);
}
}
