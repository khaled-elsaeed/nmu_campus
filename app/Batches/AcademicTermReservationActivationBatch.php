<?php

namespace App\Batches;

use App\Jobs\ActivateReservation;
use App\Models\AcademicTerm;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Throwable;

class AcademicTermReservationActivationBatch
{
    public static function create(AcademicTerm $term)
    {
        // Use chunk() to handle large datasets efficiently
        $reservationIds = $term->reservations()
            ->where('status', 'confirmed')
            ->where('active', false)
            ->pluck('id')
            ->chunk(1000) 
            ->flatten();

        $jobs = $reservationIds->map(function ($id) {
            return new ActivateReservation($id);
        })->toArray();

        $totalJobs = count($jobs);

        Log::info("Starting activation batch for term: {$term->id} ({$term->name}), total reservations: {$totalJobs}");

        if ($totalJobs === 0) {
            Log::info("No reservations to activate for term {$term->id} ({$term->name})");
            self::setTermAsCurrent($term);
        }

        return Bus::batch($jobs)
            ->name("Activate Term {$term->name} Reservations")
            ->progress(function (Batch $batch) {
                Log::info("Batch progress: {$batch->processedJobs()}/{$batch->totalJobs}");
            })
            ->then(function (Batch $batch) use ($term) {
                self::setTermAsCurrent($term);
                Log::info("Term {$term->id} ({$term->name}) set as current after batch completion. Batch ID: {$batch->id}");
            })
            ->catch(function (Batch $batch, Throwable $e) use ($term) {
                Log::error("Term activation batch failed for term {$term->id} ({$term->name}), Batch ID: {$batch->id}. Error: " . $e->getMessage());
                // Consider if you want to rollback here
            })
            ->finally(function (Batch $batch) use ($term) {
                Log::info("Activation batch for term {$term->id} ({$term->name}) finished. Batch ID: {$batch->id}, Status: {$batch->status()}");
            })
            ->dispatch();
    }

    private static function setTermAsCurrent(AcademicTerm $term)
    {
        DB::transaction(function () use ($term) {
            AcademicTerm::where('current', true)->update(['current' => false]);
            $term->update(['current' => true]);
        });
    }
}