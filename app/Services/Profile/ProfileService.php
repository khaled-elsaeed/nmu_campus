<?php

namespace App\Services\Profile;

use App\Services\Profile\ProfileDataService;
use App\Services\Profile\ProfileUpdateService;

class ProfileService
{
    /**
     * @var ProfileDataService
     */
    protected $profileDataService;

    /**
     * @var ProfileUpdateService
     */
    protected $profileUpdateService;

    public function __construct(
        ProfileDataService $profileDataService,
        ProfileUpdateService $profileUpdateService
    ) {
        $this->profileDataService = $profileDataService;
        $this->profileUpdateService = $profileUpdateService;
    }

    /**
     * Get profile data for the current authenticated user
     *
     * @return array
     */
    public function getProfileData(): array
    {
        return $this->profileDataService->getProfileData();
    }

    /**
     * Save profile data for the current authenticated user
     *
     * @param array $data
     * @return array
     */
    public function saveProfileData(array $data): array
    {
        return $this->profileUpdateService->saveProfileData($data);
    }
}
