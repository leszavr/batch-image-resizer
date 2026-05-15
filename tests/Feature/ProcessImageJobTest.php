<?php

namespace Tests\Feature;

use App\ImagePipeline\ImagePipelineService;
use App\Jobs\ProcessImageJob;
use App\Models\ImageJob;
use App\Models\ImageJobFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;
use ZipArchive;

class ProcessImageJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_process_image_job_marks_job_done_builds_zip_and_deletes_sources(): void
    {
        $job = $this->createPendingJobWithFiles([
            ['path' => 'uploads/job-success/first.jpg', 'name' => 'first.jpg'],
            ['path' => 'uploads/job-success/second.jpg', 'name' => 'second.jpg'],
        ]);

        $pipeline = Mockery::mock(ImagePipelineService::class);
        $pipeline->shouldReceive('process')
            ->twice()
            ->andReturnUsing(function (string $sourcePath, string $destPath): void {
                if (! is_dir(dirname($destPath))) {
                    mkdir(dirname($destPath), 0755, true);
                }

                copy($sourcePath, $destPath);
            });

        (new ProcessImageJob($job->id))->handle($pipeline);

        $job->refresh();
        $job->load('files');

        $this->assertSame(ImageJob::STATUS_DONE, $job->status);
        $this->assertSame(2, $job->processed_files);
        $this->assertSame(0, $job->failed_files);
        $this->assertNotNull($job->completed_at);
        $this->assertNotNull($job->result_archive_path);
        $this->assertGreaterThan(0, $job->result_size_bytes);

        foreach ($job->files as $file) {
            $this->assertSame(ImageJobFile::STATUS_DONE, $file->status);
            $this->assertNotNull($file->result_path);
            Storage::disk('local')->assertMissing($file->original_path);
            Storage::disk('local')->assertExists($file->result_path);
        }

        Storage::disk('local')->assertExists($job->result_archive_path);

        $zip = new ZipArchive();
        $zipPath = Storage::disk('local')->path($job->result_archive_path);

        $this->assertTrue($zip->open($zipPath) === true);
        $this->assertSame(2, $zip->numFiles);
        $zip->close();
    }

    public function test_process_image_job_marks_partial_failures_but_keeps_job_done(): void
    {
        $job = $this->createPendingJobWithFiles([
            ['path' => 'uploads/job-partial/ok.jpg', 'name' => 'ok.jpg'],
            ['path' => 'uploads/job-partial/fail.jpg', 'name' => 'fail.jpg'],
        ]);

        $pipeline = Mockery::mock(ImagePipelineService::class);
        $pipeline->shouldReceive('process')
            ->twice()
            ->andReturnUsing(function (string $sourcePath, string $destPath): void {
                if (str_contains($sourcePath, 'fail.jpg')) {
                    throw new \RuntimeException('Synthetic pipeline failure.');
                }

                if (! is_dir(dirname($destPath))) {
                    mkdir(dirname($destPath), 0755, true);
                }

                copy($sourcePath, $destPath);
            });

        (new ProcessImageJob($job->id))->handle($pipeline);

        $job->refresh();
        $job->load('files');

        $this->assertSame(ImageJob::STATUS_DONE, $job->status);
        $this->assertSame(1, $job->processed_files);
        $this->assertSame(1, $job->failed_files);
        $this->assertNotNull($job->result_archive_path);

        $successfulFile = $job->files->firstWhere('original_name', 'ok.jpg');
        $failedFile = $job->files->firstWhere('original_name', 'fail.jpg');

        $this->assertSame(ImageJobFile::STATUS_DONE, $successfulFile->status);
        $this->assertSame(ImageJobFile::STATUS_FAILED, $failedFile->status);
        $this->assertSame('Synthetic pipeline failure.', $failedFile->error_message);
        $this->assertNotNull($successfulFile->result_path);
        $this->assertNull($failedFile->result_path);

        Storage::disk('local')->assertExists($job->result_archive_path);
    }

    public function test_process_image_job_marks_job_failed_when_all_files_fail(): void
    {
        $job = $this->createPendingJobWithFiles([
            ['path' => 'uploads/job-failed/first.jpg', 'name' => 'first.jpg'],
            ['path' => 'uploads/job-failed/second.jpg', 'name' => 'second.jpg'],
        ]);

        $pipeline = Mockery::mock(ImagePipelineService::class);
        $pipeline->shouldReceive('process')
            ->twice()
            ->andThrow(new \RuntimeException('Total pipeline failure.'));

        (new ProcessImageJob($job->id))->handle($pipeline);

        $job->refresh();
        $job->load('files');

        $this->assertSame(ImageJob::STATUS_FAILED, $job->status);
        $this->assertSame(0, $job->processed_files);
        $this->assertSame(2, $job->failed_files);
        $this->assertNotNull($job->completed_at);
        $this->assertNull($job->result_archive_path);
        $this->assertSame(0, $job->result_size_bytes);

        foreach ($job->files as $file) {
            $this->assertSame(ImageJobFile::STATUS_FAILED, $file->status);
            $this->assertSame('Total pipeline failure.', $file->error_message);
            $this->assertNull($file->result_path);
            Storage::disk('local')->assertMissing($file->original_path);
        }
    }

    private function createPendingJobWithFiles(array $files): ImageJob
    {
        $job = ImageJob::query()->create([
            'status' => ImageJob::STATUS_PENDING,
            'pipeline' => [],
            'output_format' => 'jpg',
            'output_quality' => 85,
            'rename_mode' => 'original',
            'rename_start_number' => 1,
            'total_files' => count($files),
            'processed_files' => 0,
            'failed_files' => 0,
            'expires_at' => now()->addDay(),
        ]);

        foreach ($files as $index => $file) {
            $this->storeFakeImage($file['path']);

            ImageJobFile::query()->create([
                'image_job_id' => $job->id,
                'original_name' => $file['name'],
                'original_path' => $file['path'],
                'original_mime' => 'image/jpeg',
                'original_size' => Storage::disk('local')->size($file['path']),
                'status' => ImageJobFile::STATUS_PENDING,
                'sort_order' => $index,
            ]);
        }

        return $job->fresh();
    }

    private function storeFakeImage(string $path): void
    {
        $image = UploadedFile::fake()->image(basename($path), 120, 80);

        Storage::disk('local')->put($path, file_get_contents($image->getPathname()));
    }
}
