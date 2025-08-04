<?php

namespace App\Services\Profile;

use App\Models\Governorate;
use App\Models\City;
use App\Models\Country;
use App\Models\Nationality;
use App\Models\Academic\Faculty;

class LookupService
{
    /**
     * Get governorate ID by name
     *
     * @param string|null $governorateName
     * @return int|null
     */
    public function getGovernorateId(?string $governorateName): ?int
    {
        if (!$governorateName) {
            return null;
        }

        $governorate = Governorate::where('name_ar', $governorateName)
            ->orWhere('name_en', $governorateName)
            ->first();

        return $governorate?->id;
    }

    /**
     * Get city ID by name
     *
     * @param string|null $cityName
     * @return int|null
     */
    public function getCityId(?string $cityName): ?int
    {
        if (!$cityName) {
            return null;
        }

        $city = City::where('name_ar', $cityName)
            ->orWhere('name_en', $cityName)
            ->first();

        return $city?->id;
    }

    /**
     * Get faculty ID by name
     *
     * @param string|null $facultyName
     * @return int|null
     */
    public function getFacultyId(?string $facultyName): ?int
    {
        if (!$facultyName) {
            return null;
        }

        $faculty = Faculty::where('name_ar', $facultyName)
            ->orWhere('name_en', $facultyName)
            ->first();

        return $faculty?->id;
    }

    /**
     * Get country by name
     *
     * @param string|null $countryName
     * @return Country|null
     */
    public function getCountry(?string $countryName): ?Country
    {
        if (!$countryName) {
            return null;
        }

        return Country::where('name_ar', $countryName)
            ->orWhere('name_en', $countryName)
            ->first();
    }

    /**
     * Get nationality ID by name
     *
     * @param string|null $nationalityName
     * @return int|null
     */
    public function getNationalityId(?string $nationalityName): ?int
    {
        if (!$nationalityName) {
            return null;
        }

        $nationality = Nationality::where('name_ar', $nationalityName)
            ->orWhere('name_en', $nationalityName)
            ->first();

        if (!$nationality) {
            $country = Country::where('name_ar', $nationalityName)
                ->orWhere('name_en', $nationalityName)
                ->first();
            if ($country) {
                $nationality = Nationality::where('code', $country?->code)->first();
            }
        }

        return $nationality?->id;
    }
}
