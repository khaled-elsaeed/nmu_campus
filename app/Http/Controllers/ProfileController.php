<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\BusinessValidationException;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
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
     * @param Request $request
     * @return JsonResponse
     */
    public function submit(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                // Personal Information
                'nationalId' => 'required|string|size:14',
                'fullNameArabic' => 'required|string|min:2',
                'fullNameEnglish' => 'required|string|min:2',
                'birthDate' => 'required|date|before:today',
                'gender' => 'required|in:male,female',
                'nationality' => 'required|exists:nationalities,id',

                // Contact Information
                'email' => 'required|email',
                'mobileNumber' => 'required|string|regex:/^(010|011|012|015)[0-9]{8}$/',
                'governorate' => 'required|exists:governorates,id',
                'city' => 'required|exists:cities,id',

                // Academic Information
                'studentId' => 'required|string|min:8|max:12',
                'faculty' => 'required|exists:faculties,id',
                'program' => 'required|exists:programs,id',
                'academicYear' => 'required|in:first,second,third,fourth,fifth',
                'gpa' => 'required|numeric|between:0,4',

                // Parent Information
                'fatherName' => 'required|string|min:2',
                'motherName' => 'required|string|min:2',
                'parentMobile' => 'required|string|regex:/^(010|011|012|015)[0-9]{8}$/',
                'isParentAbroad' => 'required|in:yes,no',

                // Conditional Parent Fields
                'abroadCountry' => 'nullable|exists:countries,id',
                'livingWithParent' => 'nullable|in:yes,no',
                'parentGovernorate' => 'nullable|exists:governorates,id',
                'parentCity' => 'nullable|exists:cities,id',

                // Sibling Information
                'hasSiblingInDorm' => 'required|in:yes,no',

                // Conditional Sibling Fields
                'siblingGender' => 'nullable|in:male,female',
                'siblingName' => 'nullable|string|min:2',
                'siblingNationalId' => 'nullable|string|size:14',
                'siblingFaculty' => 'nullable|exists:faculties,id',

                // Emergency Contact
                'emergencyName' => 'required|string|min:2',
                'emergencyRelation' => 'required|string',
                'emergencyMobile' => 'required|string|regex:/^(010|011|012|015)[0-9]{8}$/',
                'emergencyAddress' => 'required|string|min:10',

                // Terms
                'termsCheckbox' => 'required|accepted',
            ]);

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