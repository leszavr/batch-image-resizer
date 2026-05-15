<?php

namespace App\Console\Commands;

use App\Models\ImageJob;
use App\Models\ImageJobFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bir:cleanup-expired-jobs 
                            {--force : Force cleanup without confirmation}
                            {--dry-run : Show what would be cleaned without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired image jobs and their files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('Searching for expired jobs...');
        
        $jobs = ImageJob::query()
            ->with('files')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->where(function ($query) {
                $query->whereNotNull('result_archive_path')
                    ->orWhereHas('files', fn ($files) => $files->whereNotNull('result_path'));
            })
            ->get();

        if ($jobs->isEmpty()) {
            $this->info('No expired jobs found.');
            return Command::SUCCESS;
        }

        $this->line("Found {$jobs->count()} expired jobs with files to clean up.");

        if (!$dryRun && !$force) {
            if (!$this->confirm('Do you want to proceed with cleanup?')) {
                $this->info('Cleanup cancelled.');
                return Command::SUCCESS;
            }
        }

        $cleanedJobs = 0;
        $deletedFiles = [];

        foreach ($jobs as $job) {
            $paths = array_filter(array_merge(
                [$job->result_archive_path],
                $job->files->pluck('result_path')->filter()->all(),
            ));

            if ($paths !== []) {
                $this->line("Processing job {$job->uuid} ({$job->id})...");
                
                if ($dryRun) {
                    foreach ($paths as $path) {
                        $deletedFiles[] = $path;
                        $this->line("  Would delete: {$path}");
                    }
                } else {
                    Storage::disk('local')->delete($paths);
                    
                    foreach ($paths as $path) {
                        $deletedFiles[] = $path;
                        $this->line("  Deleted: {$path}");
                    }
                }
            }

            if (!$dryRun) {
                $job->files()->whereNotNull('result_path')->update(['result_path' => null, 'result_size' => 0]);

                $job->update([
                    'status' => ImageJob::STATUS_EXPIRED,
                    'result_archive_path' => null,
                    'result_size_bytes' => 0,
                ]);
            }

            $cleanedJobs++;
        }

        if ($dryRun) {
            $this->line("DRY RUN: Would clean up {$cleanedJobs} jobs and " . count($deletedFiles) . " files.");
        } else {
            $this->info("Successfully cleaned up {$cleanedJobs} jobs and " . count($deletedFiles) . " files.");
        }

        // Log the cleanup
        if (!$dryRun) {
            \App\Models\UsageLog::record('cleanup_expired_jobs', [
                'jobs_cleaned' => $cleanedJobs,
                'files_deleted' => count($deletedFiles),
                'files_list' => $deletedFiles,
            ]);
        }

        return Command::SUCCESS;
    }
}