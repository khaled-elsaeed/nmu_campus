<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class ApiClientService
{
    protected $baseUrl;
    protected $username;
    protected $password;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = env('STUDENT_ARCHIVE_API_BASE_URL');
        $this->username = env('STUDENT_ARCHIVE_API_USERNAME');
        $this->password = env('STUDENT_ARCHIVE_API_PASSWORD');
        $this->timeout = (int) (30000);
    }

    /**
     * Get students from external API
     *
     * @return array
     */
    public function getStudents(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl);

            if ($response->successful()) {
                $data = $response->json();
                $count = $data['count'] ?? count($data['students'] ?? $data);
                
                Log::info('Successfully fetched students from API', [
                    'count' => $count,
                    'status' => $response->status()
                ]);
                
                return $data; // Return the full response object
            } else {
                Log::error('Failed to fetch students from API', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                throw new \Exception('API request failed: ' . $response->status());
            }
            
        } catch (\Exception $e) {
            Log::error('Exception while fetching students from API: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get students count from external API
     *
     * @return int
     */
    public function getStudentsCount(): int
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl);

            if ($response->successful()) {
                $data = $response->json();
                return $data['count'] ?? count($data['students'] ?? $data);
            } else {
                Log::error('Failed to fetch students count from API', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                throw new \Exception('API request failed: ' . $response->status());
            }
            
        } catch (\Exception $e) {
            Log::error('Exception while fetching students count from API: ' . $e->getMessage());
            throw $e;
        }
    }
}