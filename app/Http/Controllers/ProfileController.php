<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\profile\resident\student\CompleteRequest;
use App\Services\Profile\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\BusinessValidationException;
use Exception;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Display the profile completion page
     *
     * @return \Illuminate\View\View
     */
    public function index(): \Illuminate\View\View
    {
        return view('profile.resident.student.complete');
    }

    /**
     * Fetch the current user's profile data
     *
     * @return JsonResponse
     */
    public function fetch(): JsonResponse
    {
        try {
            $profileData = $this->profileService->getProfileData();
            return successResponse('Profile data retrieved successfully.', $profileData);

        } catch (Exception $e) {
            logError('ProfileController@fetch', $e);
            return errorResponse('Internal server error.', [], 500);
        }
    }

    /**
     * Submit/update the user's profile data
     *
     * @param CompleteRequest $request
     * @return JsonResponse
     */
    public function submit(CompleteRequest $request): JsonResponse
    {
        try {
            // Get validated data from the form request
            $validatedData = $request->validated();

            $result = $this->profileService->saveProfileData($validatedData);

            return successResponse('Profile updated successfully.', $result);

        } catch (BusinessValidationException $e) {
            return errorResponse($e->getMessage());
        } catch (Exception $e) {
            logError('ProfileController@submit', $e);
            return errorResponse('Failed to save profile: ' . $e->getMessage(), [], 500);
        }
    }
}