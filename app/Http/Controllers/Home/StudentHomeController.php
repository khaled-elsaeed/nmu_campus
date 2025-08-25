<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Services\Home\StudentHomeService;
use Illuminate\Http\{Request, JsonResponse};
use Exception;


class StudentHomeController extends Controller
{
    /**
     * StudentHomeController constructor.
     *
     * @param StudentHomeService $studentHomeService
     */
    public function __construct(protected StudentHomeService $studentHomeService)
    {}

    /**
     * Display the student home page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('home.student');
    }

    public function userDetail() : jsonResponse
    {
        try {
            $stats = $this->studentHomeService->getUserDetails();
            return successResponse(__('Student details fetched successfully'), $stats);
        } catch (Exception $e) {
            logError('StudentHomeController@userDetail', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    public function activeReservationDetails() : jsonResponse
    {
        try {
            $reservations = $this->studentHomeService->getActiveReservation();
            return successResponse(__('Active reservations fetched successfully'), $reservations);
        } catch (Exception $e) {
            logError('StudentHomeController@activeReservationDetails', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    public function getActiveReservationNeighbors() : jsonResponse
    {
        try {
            $neighbors = $this->studentHomeService->getActiveReservationNeighbors();
            return successResponse(__('Active reservation neighbors fetched successfully'), $neighbors);
        } catch (Exception $e) {
            logError('StudentHomeController@getActiveReservationNeighbors', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    public function getUpcomingEvents() : jsonResponse
    {
        try {
            $events = $this->studentHomeService->getUpcomingEvents();
            return successResponse(__('Upcoming events fetched successfully'), $events);
        } catch (Exception $e) {
            logError('StudentHomeController@getUpcomingEvents', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    public function getReservationRequests() : jsonResponse
    {
        try {
            $requests = $this->studentHomeService->getReservationRequests();
            return successResponse(__('Reservation requests fetched successfully'), $requests);
        } catch (Exception $e) {
            logError('StudentHomeController@getReservationRequests', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

    public function getNewRequestData() : jsonResponse
    {
        try {
            $data = $this->studentHomeService->getNewRequestData();
            return successResponse(__('New request data fetched successfully'), $data);
        } catch (Exception $e) {
            logError('StudentHomeController@getNewRequestData', $e);
            return errorResponse(__('Internal server error'), [], 500);
        }
    }

}