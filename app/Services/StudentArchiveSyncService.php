<?php

namespace App\Services;

use App\Models\StudentArchive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class StudentArchiveSyncService
{
    protected $apiClient;
    
    public function __construct(ApiClientService $apiClient)
    {
        $this->apiClient = $apiClient;
    }
    
    /**
     * Archive/Update students and mark deleted ones
     */
    public function syncWithApi(): array
    {
        try {
            DB::beginTransaction();
            
            // Get fresh data from API
            $apiResponse = $this->apiClient->getStudents();
            $apiStudents = $this->fetchApiData($apiResponse);
            
            // Log API response statistics
            $apiCount = $apiResponse['count'] ?? $apiStudents->count();
            Log::info('Starting student archive sync', [
                'api_count' => $apiCount,
                'processed_count' => $apiStudents->count()
            ]);
            
            // Process sync - only create/update/delete
            $result = $this->performArchiveSync($apiStudents);
            
            // Add API count to result
            $result['api_count'] = $apiCount;
            
            DB::commit();
            Log::info('Student archive sync completed successfully', $result);
            
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Student archive sync failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Fetch data from API
     */
    protected function fetchApiData(array $apiResponse): Collection
    {
        // Handle the API response format with count and students wrapper
        $students = $apiResponse['students'] ?? $apiResponse;
        
        return collect($students)->map(function ($student) {
            return [
                'external_id' => $student['id'],
                'name_ar' => $student['name_ar'] ?? null,
                'name_en' => $student['name_en'] ?? null,
                'email' => $student['email'] ?? null,
                'academic_id' => $student['academic_id'] ?? null,
                'academic_email' => $student['academic_email'] ?? null,
                'cum_gpa' => $student['cum_gpa'] ?? null,
                'national_id' => $student['national_id'] ?? null,
                // API is 'mobile', DB is 'phone'
                'phone' => $this->validateAndCleanPhone($student['mobile'] ?? null),
                'whatsapp' => $this->validateAndCleanPhone($student['whatsapp'] ?? null),
                'birthdate' => isset($student['birthdate']) ? Carbon::parse($student['birthdate']) : null,
                'gender' => $student['gender'] ?? null,
                'nationality_name' => $student['nationality_name'] ?? null,
                'govern' => $student['govern'] ?? null,
                'city' => $student['city'] ?? null,
                'street' => $student['street'] ?? null,
                'parent_name' => $student['parent_name'] ?? null,
                // API is 'parent_mobile', DB is 'parent_phone'
                'parent_phone' => $this->validateAndCleanPhone($student['parent_mobile'] ?? null),
                'parent_email' => $student['parent_email'] ?? null,
                'parent_country_name' => $student['parent_country_name'] ?? null,
                'certificate_type_name' => $student['certificate_type_name'] ?? null,
                'cert_country_name' => $student['cert_country_name'] ?? null,
                'cert_year_name' => $student['cert_year_name'] ?? null,
                'brother' => $student['brother'] ?? null,
                'brother_name' => $student['brother_name'] ?? null,
                'brother_faculty' => $student['brother_faculty'] ?? null,
                'brother_faculty_name' => $student['brother_faculty_name'] ?? null,
                'brother_level' => $student['brother_level'] ?? null,
                'candidated_faculty_name' => $student['candidated_faculty_name'] ?? null,
                'actual_score' => $student['actual_score'] ?? null,
                'actual_percent' => $student['actual_percent'] ?? null,
                'last_updated_at' => isset($student['updated_at']) ? Carbon::parse($student['updated_at']) : now(),
            ];
        });
    }

    /**
     * Validate and clean phone number
     * Returns null if the phone number is invalid or too long
     */
    protected function validateAndCleanPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Remove any non-digit characters
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if the cleaned number is within reasonable length (5-15 digits)
        if (strlen($cleaned) < 5 || strlen($cleaned) > 15) {
            Log::warning('Invalid phone number length detected', [
                'original' => $phone,
                'cleaned' => $cleaned,
                'length' => strlen($cleaned)
            ]);
            return null;
        }

        // Check if it's a reasonable phone number format
        // Most phone numbers should be between 10-15 digits
        if (strlen($cleaned) > 20) {
            Log::warning('Phone number too long, skipping', [
                'original' => $phone,
                'cleaned' => $cleaned,
                'length' => strlen($cleaned)
            ]);
            return null;
        }

        return $cleaned;
    }
    
    /**
     * Perform archive synchronization - create, update, or mark as deleted
     */
    protected function performArchiveSync(Collection $apiStudents): array
    {
        $stats = [
            'created' => 0,
            'updated' => 0,
            'marked_deleted' => 0,
            'total_processed' => $apiStudents->count()
        ];
        
        // Get all existing records (including soft deleted ones)
        $existingStudents = StudentArchive::withTrashed()->get()->keyBy('external_id');
        $apiStudentIds = $apiStudents->pluck('external_id');
        
        // Process each API student
        foreach ($apiStudents as $apiStudent) {
            if ($existingStudents->has($apiStudent['external_id'])) {
                // Update existing record
                $existing = $existingStudents->get($apiStudent['external_id']);
                
                if ($this->shouldUpdate($existing, $apiStudent)) {
                    $this->updateStudentArchive($existing, $apiStudent);
                    $stats['updated']++;
                }
                
            } else {
                // Create new archive record
                $this->createStudentArchive($apiStudent);
                $stats['created']++;
            }
        }
        
        // Mark records as deleted (soft delete) if they're not in API response
        $toMarkDeleted = $existingStudents->keys()
            ->diff($apiStudentIds)
            ->filter(function($externalId) use ($existingStudents) {
                $student = $existingStudents->get($externalId);
                return is_null($student->deleted_at);
            });
            
        if ($toMarkDeleted->isNotEmpty()) {
            StudentArchive::whereIn('external_id', $toMarkDeleted)
                ->whereNull('deleted_at')
                ->update([
                    'synced_at' => now()
                ]);
            // Use Eloquent's soft delete
            StudentArchive::whereIn('external_id', $toMarkDeleted)
                ->whereNull('deleted_at')
                ->delete();
            $stats['marked_deleted'] = $toMarkDeleted->count();
        }
        
        return $stats;
    }
    
    /**
     * Check if archive record should be updated
     */
    protected function shouldUpdate(StudentArchive $existing, array $apiData): bool
    {
        // Update if API data is newer or if data has changed
        // Also update if the record is soft deleted (deleted_at is not null)
        return $existing->last_updated_at < $apiData['last_updated_at'] ||
               $existing->name_ar !== $apiData['name_ar'] ||
               $existing->name_en !== $apiData['name_en'] ||
               $existing->email !== $apiData['email'] ||
               $existing->national_id !== $apiData['national_id'] ||
               !is_null($existing->deleted_at); // Always update if previously soft deleted
    }
    
    /**
     * Update existing student archive
     */
    protected function updateStudentArchive(StudentArchive $student, array $data): void
    {
        // If the record is soft deleted, restore it
        if (method_exists($student, 'restore') && !is_null($student->deleted_at)) {
            $student->restore();
        }

        $student->update([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'email' => $data['email'],
            'national_id' => $data['national_id'],
            // API is 'mobile', DB is 'phone'
            'phone' => $data['phone'],
            'whatsapp' => $data['whatsapp'],
            'birthdate' => $data['birthdate'],
            'gender' => $data['gender'],
            'nationality_name' => $data['nationality_name'],
            'govern' => $data['govern'],
            'city' => $data['city'],
            'street' => $data['street'],
            'parent_name' => $data['parent_name'],
            // API is 'parent_mobile', DB is 'parent_phone'
            'parent_phone' => $data['parent_phone'],
            'parent_email' => $data['parent_email'],
            'parent_country_name' => $data['parent_country_name'],
            'certificate_type_name' => $data['certificate_type_name'],
            'cert_country_name' => $data['cert_country_name'],
            'cert_year_name' => $data['cert_year_name'],
            'brother' => $data['brother'],
            'brother_name' => $data['brother_name'],
            'brother_faculty' => $data['brother_faculty'],
            'brother_faculty_name' => $data['brother_faculty_name'],
            'brother_level' => $data['brother_level'],
            'candidated_faculty_name' => $data['candidated_faculty_name'],
            'actual_score' => $data['actual_score'],
            'actual_percent' => $data['actual_percent'],
            'academic_id' => $student['academic_id'] ?? null,
            'academic_email' => $student['academic_email'] ?? null,
            'cum_gpa' => $student['cum_gpa'] ?? null,
            'last_updated_at' => $data['last_updated_at'],
            'synced_at' => now(),
        ]);
    }
    
    /**
     * Create new student archive
     */
    protected function createStudentArchive(array $data): StudentArchive
    {
        return StudentArchive::create([
            'external_id' => $data['external_id'],
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'email' => $data['email'],
            'national_id' => $data['national_id'],
            // API is 'mobile', DB is 'phone'
            'phone' => $data['phone'],
            'whatsapp' => $data['whatsapp'],
            'birthdate' => $data['birthdate'],
            'gender' => $data['gender'],
            'nationality_name' => $data['nationality_name'],
            'govern' => $data['govern'],
            'city' => $data['city'],
            'street' => $data['street'],
            'parent_name' => $data['parent_name'],
            // API is 'parent_mobile', DB is 'parent_phone'
            'parent_phone' => $data['parent_phone'],
            'parent_email' => $data['parent_email'],
            'parent_country_name' => $data['parent_country_name'],
            'certificate_type_name' => $data['certificate_type_name'],
            'cert_country_name' => $data['cert_country_name'],
            'cert_year_name' => $data['cert_year_name'],
            'brother' => $data['brother'],
            'brother_name' => $data['brother_name'],
            'brother_faculty' => $data['brother_faculty'],
            'brother_faculty_name' => $data['brother_faculty_name'],
            'brother_level' => $data['brother_level'],
            'candidated_faculty_name' => $data['candidated_faculty_name'],
            'actual_score' => $data['actual_score'],
            'actual_percent' => $data['actual_percent'],
            'academic_id' => $data['academic_id'] ?? null,
            'academic_email' => $data['academic_email'] ?? null,
            'cum_gpa' => $data['cum_gpa'] ?? null,
            'last_updated_at' => $data['last_updated_at'],
            'synced_at' => now(),
        ]);
    }
    
    /**
     * Get sync statistics
     */
    public function getSyncStats(): array
    {
        return [
            'total_records' => StudentArchive::withTrashed()->count(),
            'active_records' => StudentArchive::count(),
            'deleted_records' => StudentArchive::onlyTrashed()->count(),
            'last_sync' => StudentArchive::withTrashed()->max('synced_at'),
        ];
    }
}