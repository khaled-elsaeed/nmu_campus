<?php

namespace App\Services\Reservation\Pipeline;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class ReservationPipeline
{
    /**
     * Execute the pipeline within a database transaction
     *
     * @param array $data
     * @param array $pipes
     * @param callable|null $finally
     * @return mixed
     */
    protected function executePipeline(array $data, array $pipes, callable $finally = null)
    {
        return DB::transaction(function () use ($data, $pipes, $finally) {
            $result = app(Pipeline::class)
                ->send($data)
                ->through($pipes)
                ->thenReturn();

            if ($finally) {
                $finally($result, $data);
            }

            return $result;
        });
    }

    /**
     * Execute the pipeline with finally method for cleanup
     *
     * @param array $data
     * @param array $pipes
     * @param callable|null $finally
     * @return mixed
     */
    protected function executePipelineWithFinally(array $data, array $pipes, callable $finally = null)
    {
        return DB::transaction(function () use ($data, $pipes, $finally) {
            $pipeline = app(Pipeline::class)
                ->send($data)
                ->through($pipes);

            if ($finally) {
                $pipeline->finally($finally);
            }

            return $pipeline->thenReturn();
        });
    }

    /**
     * Log pipeline execution for debugging
     *
     * @param string $operation
     * @param array $data
     * @param mixed $result
     */
    protected function logPipelineExecution(string $operation, array $data, $result): void
    {
        Log::info("Reservation pipeline executed", [
            'operation' => $operation,
            'data_keys' => array_keys($data),
            'result_type' => gettype($result),
            'timestamp' => now()->toISOString()
        ]);
    }
}
