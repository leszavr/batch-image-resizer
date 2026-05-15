<?php

namespace App\Jobs;

use App\ImagePipeline\ImagePipelineService;
use App\Models\ImageJob;
use App\Models\ImageJobFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 2;

    public function __construct(public readonly int $imageJobId)
    {
        $this->onQueue(config('bir.queue', 'image-processing'));
    }

    public function handle(ImagePipelineService $pipeline): void
    {
        $job = ImageJob::with('files')->findOrFail($this->imageJobId);

        if ($job->status !== ImageJob::STATUS_PENDING) {
            return; // already processed or cancelled
        }

        $job->update([
            'status'     => ImageJob::STATUS_PROCESSING,
            'started_at' => now(),
        ]);

        $processedPaths = [];

        foreach ($job->files as $index => $file) {
            $file->update(['status' => ImageJobFile::STATUS_PROCESSING]);

            try {
                $sourcePath = Storage::disk('local')->path($file->original_path);

                $resultFilename = $job->buildResultFilename($file->original_name, $index);
                $resultRelPath  = "results/{$job->uuid}/{$resultFilename}";
                $resultAbsPath  = Storage::disk('local')->path($resultRelPath);

                $pipeline->process(
                    sourcePath:   $sourcePath,
                    destPath:     $resultAbsPath,
                    pipeline:     $job->pipeline ?? [],
                    outputFormat: $job->output_format ?? 'jpg',
                    quality:      $job->output_quality ?? 85,
                );

                $resultSize = filesize($resultAbsPath);

                [$resultW, $resultH] = @getimagesize($resultAbsPath) ?: [null, null];

                $file->update([
                    'status'        => ImageJobFile::STATUS_DONE,
                    'result_path'   => $resultRelPath,
                    'result_size'   => $resultSize,
                    'result_width'  => $resultW,
                    'result_height' => $resultH,
                ]);

                $processedPaths[] = $resultAbsPath;

                $job->increment('processed_files');

            } catch (\Throwable $e) {
                Log::error("ProcessImageJob file #{$file->id} failed: " . $e->getMessage());

                $file->update([
                    'status'        => ImageJobFile::STATUS_FAILED,
                    'error_message' => $e->getMessage(),
                ]);

                $job->increment('failed_files');
            }
        }

        $archivePath = null;
        $archiveSize = 0;

        if ($processedPaths !== []) {
            $archivePath = "archives/{$job->uuid}.zip";
            $archiveAbs  = Storage::disk('local')->path($archivePath);

            $this->buildZip($processedPaths, $archiveAbs);

            $archiveSize = file_exists($archiveAbs) ? filesize($archiveAbs) : 0;
        }

        $finalStatus = $job->fresh()->failed_files === $job->total_files
            ? ImageJob::STATUS_FAILED
            : ImageJob::STATUS_DONE;

        $job->update([
            'status'               => $finalStatus,
            'completed_at'         => now(),
            'result_archive_path'  => $archivePath,
            'result_size_bytes'    => $archiveSize,
            'expires_at'           => now()->addHours((int) config('bir.storage_ttl_hours', 24)),
        ]);

        // Clean up source files to save disk space
        foreach ($job->files as $file) {
            Storage::disk('local')->delete($file->original_path);
        }
    }

    private function buildZip(array $filePaths, string $destPath): void
    {
        $dir = dirname($destPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($destPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create ZIP archive: {$destPath}");
        }

        foreach ($filePaths as $path) {
            if (file_exists($path)) {
                $zip->addFile($path, basename($path));
            }
        }

        $zip->close();
    }

    public function failed(\Throwable $e): void
    {
        Log::error("ProcessImageJob #{$this->imageJobId} completely failed: " . $e->getMessage());

        ImageJob::where('id', $this->imageJobId)->update([
            'status'       => ImageJob::STATUS_FAILED,
            'completed_at' => now(),
        ]);
    }
}
