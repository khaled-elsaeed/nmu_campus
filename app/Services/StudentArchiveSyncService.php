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
            
            $apiResponse = $this->apiClient->getStudents();
            $apiStudents = $this->fetchApiData($apiResponse);
            
            $apiCount = $apiResponse['count'] ?? $apiStudents->count();

            $result = $this->performArchiveSync($apiStudents);
            
            $result['api_count'] = $apiCount;
            
            DB::commit();            
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error using api_errors channel
            Log::channel('api_errors')->error('Student archive sync failed', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Fetch data from API
     */
    protected function fetchApiData(array $apiResponse): Collection
    {
        try {
            // Handle the API response format with count and students wrapper
            $students = $apiResponse['students'] ?? $apiResponse;
            
            return collect($students)->map(function ($student) {
                try {
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
                        // API is 'parent_*', DB is 'guardian_*'
                        'guardian_name' => $student['parent_name'] ?? null,
                        'guardian_phone' => $this->validateAndCleanPhone($student['parent_mobile'] ?? null),
                        'guardian_email' => $student['parent_email'] ?? null,
                        'guardian_country_name' => $student['parent_country_name'] ?? null,
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
                } catch (\Exception $e) {
                    // Log individual student processing error and skip this student
                    Log::channel('api_errors')->warning('Failed to process student data - skipping student', [
                        'student_id' => $student['id'] ?? 'unknown',
                        'error_message' => $e->getMessage(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                        'student_data' => $student
                    ]);
                    return null; // This will be filtered out
                }
            })->filter(); // Remove null entries from failed processing
            
        } catch (\Exception $e) {
            Log::channel('api_errors')->error('Failed to fetch API data', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'api_response_keys' => array_keys($apiResponse)
            ]);
            throw $e;
        }
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

        try {
            // Remove any non-digit characters
            $cleaned = preg_replace('/[^0-9]/', '', $phone);
            
            // Check if the cleaned number is within reasonable length (5-15 digits)
            if (strlen($cleaned) < 5 || strlen($cleaned) > 15) {
                Log::channel('api_errors')->warning('Invalid phone number length detected', [
                    'original' => $phone,
                    'cleaned' => $cleaned,
                    'length' => strlen($cleaned)
                ]);
                return null;
            }

            // Check if it's a reasonable phone number format
            // Most phone numbers should be between 10-15 digits
            if (strlen($cleaned) > 20) {
                Log::channel('api_errors')->warning('Phone number too long, skipping', [
                    'original' => $phone,
                    'cleaned' => $cleaned,
                    'length' => strlen($cleaned)
                ]);
                return null;
            }

            return $cleaned;
        } catch (\Exception $e) {
            Log::channel('api_errors')->warning('Error validating phone number', [
                'phone' => $phone,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);
            return null;
        }
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
            'errors' => 0,
            'skipped_students' => [],
            'total_processed' => $apiStudents->count()
        ];
        
        try {
            // Get all existing records (including soft deleted ones)
            $existingStudents = StudentArchive::withTrashed()->get()->keyBy('external_id');
            $apiStudentIds = $apiStudents->pluck('external_id');
            
            // Process each API student
            foreach ($apiStudents as $apiStudent) {
                try {
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
                } catch (\Exception $e) {
                    $stats['errors']++;
                    $studentId = $apiStudent['external_id'] ?? 'unknown';
                    $stats['skipped_students'][] = $studentId;
                    
                    Log::channel('api_errors')->error('Failed to sync individual student - skipping student', [
                        'external_id' => $studentId,
                        'error_message' => $e->getMessage(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                        'stack_trace' => $e->getTraceAsString(),
                        'student_data' => $apiStudent
                    ]);
                    // Continue processing other students
                    continue;
                }
            }
            
            // Mark records as deleted (soft delete) if they're not in API response
            $this->markDeletedStudents($existingStudents, $apiStudentIds, $stats);
            
            return $stats;
            
        } catch (\Exception $e) {
            Log::channel('api_errors')->error('Failed to perform archive sync', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'stats' => $stats
            ]);
            throw $e;
        }
    }
    
    /**
     * Mark students as deleted (soft delete) if they're not in API response
     */
    protected function markDeletedStudents(Collection $existingStudents, Collection $apiStudentIds, array &$stats): void
    {
        try {
            $toMarkDeleted = $existingStudents->keys()
                ->diff($apiStudentIds)
                ->filter(function($externalId) use ($existingStudents) {
                    $student = $existingStudents->get($externalId);
                    return is_null($student->deleted_at);
                });
                
            if ($toMarkDeleted->isNotEmpty()) {
                try {
                    StudentArchive::whereIn('external_id', $toMarkDeleted)
                        ->whereNull('deleted_at')
                        ->update([
                            'synced_at' => now()
                        ]);

                        StudentArchive::whereIn('external_id', $toMarkDeleted)
                        ->whereNull('deleted_at')
                        ->delete();
                    $stats['marked_deleted'] = $toMarkDeleted->count();
  
                } catch (\Exception $e) {
                    Log::channel('api_errors')->error('Failed to mark students as deleted', [
                        'error_message' => $e->getMessage(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                        'students_to_delete_count' => $toMarkDeleted->count(),
                        'students_to_delete' => $toMarkDeleted->toArray(),
                        'stack_trace' => $e->getTraceAsString()
                    ]);
                    // Don't throw here, just log and continue
                }
            }
        } catch (\Exception $e) {
            Log::channel('api_errors')->error('Failed to process deleted students marking', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            // Don't throw here, just log and continue
        }
    }
    
    /**
     * Check if archive record should be updated
     */
    protected function shouldUpdate(StudentArchive $existing, array $apiData): bool
    {
        try {
            // Update if API data is newer or if data has changed
            // Also update if the record is soft deleted (deleted_at is not null)
            return $existing->last_updated_at < $apiData['last_updated_at'] ||
                   $existing->name_ar !== $apiData['name_ar'] ||
                   $existing->name_en !== $apiData['name_en'] ||
                   $existing->email !== $apiData['email'] ||
                   $existing->national_id !== $apiData['national_id'] ||
                   !is_null($existing->deleted_at); // Always update if previously soft deleted
        } catch (\Exception $e) {
            Log::channel('api_errors')->error('Failed to check if student should be updated', [
                'external_id' => $existing->external_id ?? 'unknown',
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);
            // Return true to attempt update anyway
            return true;
        }
    }
    
    /**
     * Update existing student archive
     */
    protected function updateStudentArchive(StudentArchive $student, array $data): void
    {
        try {
            // If the record is soft deleted, restore it
            if (method_exists($student, 'restore') && !is_null($student->deleted_at)) {
                $student->restore();
            }

            $student->update([
    'name_ar' => $data['name_ar'],
    'name_en' => $data['name_en'],
    'email' => $data['email'],
    'national_id' => $data['national_id'],
    'phone' => $data['phone'],
    'whatsapp' => $data['whatsapp'],
    'birthdate' => $data['birthdate'],
    'gender' => $data['gender'],
    'nationality_name' => $data['nationality_name'],
    'govern' => $data['govern'],
    'city' => $data['city'],
    'street' => $data['street'],

    // ✅ use guardian_* (already mapped in fetchApiData)
    'guardian_name' => $data['guardian_name'],
    'guardian_phone' => $data['guardian_phone'],
    'guardian_email' => $data['guardian_email'],
    'guardian_country_name' => $data['guardian_country_name'],

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

            
        } catch (\Exception $e) {
            Log::channel('api_errors')->error('Failed to update student archive', [
                'external_id' => $data['external_id'] ?? 'unknown',
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Create new student archive
     */
    protected function createStudentArchive(array $data): StudentArchive
    {
        try {
            $student = StudentArchive::create([
    'external_id' => $data['external_id'],
    'name_ar' => $data['name_ar'],
    'name_en' => $data['name_en'],
    'email' => $data['email'],
    'national_id' => $data['national_id'],
    'phone' => $data['phone'],
    'whatsapp' => $data['whatsapp'],
    'birthdate' => $data['birthdate'],
    'gender' => $data['gender'],
    'nationality_name' => $data['nationality_name'],
    'govern' => $data['govern'],
    'city' => $data['city'],
    'street' => $data['street'],

    // ✅ use guardian_* (already mapped in fetchApiData)
    'guardian_name' => $data['guardian_name'],
    'guardian_phone' => $data['guardian_phone'],
    'guardian_email' => $data['guardian_email'],
    'guardian_country_name' => $data['guardian_country_name'],

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

 
            return $student;
            
        } catch (\Exception $e) {
            Log::channel('api_errors')->error('Failed to create student archive', [
                'external_id' => $data['external_id'] ?? 'unknown',
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'student_data' => $data
            ]);
            throw $e;
        }
    }
    
    /**
     * Get sync statistics
     */
    public function getSyncStats(): array
    {
        try {
            return [
                'total_records' => StudentArchive::withTrashed()->count(),
                'active_records' => StudentArchive::count(),
                'deleted_records' => StudentArchive::onlyTrashed()->count(),
                'last_sync' => StudentArchive::withTrashed()->max('synced_at'),
            ];
        } catch (\Exception $e) {
            Log::channel('api_errors')->error('Failed to get sync statistics', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);
            
            // Return default stats if query fails
            return [
                'total_records' => 0,
                'active_records' => 0,
                'deleted_records' => 0,
                'last_sync' => null,
                'error' => 'Failed to retrieve statistics'
            ];
        }
    }
}