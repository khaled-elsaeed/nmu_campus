<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\BusinessValidationException;
use Exception; // Added missing import

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
                'nationality' => [
                    'required',
                    'exists:nationalities,id'
                ],

                // Contact Information
                'phone' => [
                    'required',
                    'string',
                    'regex:/^(010|011|012|015)[0-9]{8}$/'
                ],
                'governorate' => [
                    'required',
                    'exists:governorates,id'
                ],
                'city' => [
                    'required',
                    'exists:cities,id'
                ],
                'street' => [
                    'required',
                    'string',
                    'min:3'
                ],

                // Academic Information
                'program' => [
                    'required',
                    'exists:programs,id'
                ],
                'academicYear' => [
                    'required',
                    'in:1,2,3,4,5'
                ],

                // Parent Information
                'parentRelationship' => [
                    'required',
                    'in:father,mother'
                ],
                'parentName' => [
                    'required',
                    'string',
                    'min:2'
                ],
                'parentPhone' => [
                    'required',
                    'string'
                ],
                'parentEmail' => [
                    'nullable',
                    'email'
                ],
                'isParentAbroad' => [
                    'required',
                    'in:yes,no'
                ],

                // Conditional Parent Fields
                'abroadCountry' => [
                    'nullable',
                    'exists:countries,id'
                ],
                'livingWithParent' => [
                    'nullable',
                    'in:yes,no'
                ],
                'parentGovernorate' => [
                    'nullable',
                    'exists:governorates,id'
                ],
                'parentCity' => [
                    'nullable',
                    'exists:cities,id'
                ],

                // Sibling Information
                'hasSiblingInDorm' => [
                    'required',
                    'in:yes,no'
                ],

                // Conditional Sibling Fields
                'siblingGender' => [
                    'nullable',
                    'in:male,female'
                ],
                'siblingName' => [
                    'nullable',
                    'string',
                    'min:2'
                ],
                'siblingNationalId' => [
                    'nullable',
                    'string',
                    'size:14'
                ],
                'siblingFaculty' => [
                    'nullable',
                    'exists:faculties,id'
                ],

                // Emergency Contact
                'emergencyContactName' => [
                    'nullable',
                    'string',
                    'min:2'
                ],
                'emergencyContactRelationship' => [
                    'nullable',
                    'string'
                ],
                'emergencyContactPhone' => [
                    'nullable',
                    'string',
                    'regex:/^(010|011|012|015)[0-9]{8}$/'
                ],

                // Terms
                'termsCheckbox' => [
                    'required',
                    'accepted'
                ],
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