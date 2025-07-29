<?php

namespace App\Console\Commands;

use App\Services\StudentArchiveSyncService;
use Illuminate\Console\Command;

class SyncStudentArchive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:archive-sync 
                           {--stats : Show current sync statistics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive student data from external API';

    /**
     * Execute the console command.
     */
    public function handle(StudentArchiveSyncService $syncService): int
    {
        if ($this->option('stats')) {
            $this->displayStats($syncService);
            return 0;
        }
        
        $this->info('Starting student archive synchronization...');
        
        try {
            $result = $syncService->syncWithApi();
            
            $this->info('Archive sync completed successfully:');
            $this->table(
                ['Action', 'Count'],
                [
                    ['API Records Available', $result['api_count'] ?? 'N/A'],
                    ['New Records Created', $result['created']],
                    ['Existing Records Updated', $result['updated']],
                    ['Records Marked as Deleted', $result['marked_deleted']],
                    ['Total API Records Processed', $result['total_processed']],
                ]
            );
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Display sync statistics
     */
    protected function displayStats(StudentArchiveSyncService $syncService): void
    {
        $stats = $syncService->getSyncStats();
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Archive Records', $stats['total_records']],
                ['Active Records', $stats['active_records']],
                ['Deleted Records', $stats['deleted_records']],
                ['Last Sync', $stats['last_sync'] ?: 'Never'],
            ]
        );
    }
}