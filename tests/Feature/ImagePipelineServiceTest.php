<?php

namespace Tests\Feature;

use App\ImagePipeline\ImagePipelineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ImagePipelineServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_process_applies_multiple_steps_and_saves_result_file(): void
    {
        $service = app(ImagePipelineService::class);

        $sourceRelativePath = 'uploads/pipeline/source.jpg';
        $destRelativePath = 'results/pipeline/output.jpg';

        $this->storeFakeImage($sourceRelativePath, 120, 80);

        $service->process(
            sourcePath: Storage::disk('local')->path($sourceRelativePath),
            destPath: Storage::disk('local')->path($destRelativePath),
            pipeline: [
                [
                    'step' => 'resize',
                    'params' => ['mode' => 'width', 'width' => 60, 'upscale' => true],
                ],
                [
                    'step' => 'rotate',
                    'params' => ['direction' => 'left'],
                ],
            ],
            outputFormat: 'jpg',
            quality: 82,
        );

        Storage::disk('local')->assertExists($destRelativePath);

        [$width, $height] = getimagesize(Storage::disk('local')->path($destRelativePath));

        $this->assertSame(40, $width);
        $this->assertSame(60, $height);
    }

    public function test_process_skips_unknown_step_and_still_writes_output(): void
    {
        $service = app(ImagePipelineService::class);

        $sourceRelativePath = 'uploads/pipeline/source-unknown.jpg';
        $destRelativePath = 'results/pipeline/unknown-step/output.png';

        $this->storeFakeImage($sourceRelativePath, 150, 90);

        $service->process(
            sourcePath: Storage::disk('local')->path($sourceRelativePath),
            destPath: Storage::disk('local')->path($destRelativePath),
            pipeline: [
                ['step' => 'non_existing_step', 'params' => ['foo' => 'bar']],
            ],
            outputFormat: 'png',
            quality: 90,
        );

        Storage::disk('local')->assertExists($destRelativePath);

        [$width, $height] = getimagesize(Storage::disk('local')->path($destRelativePath));

        $this->assertSame(150, $width);
        $this->assertSame(90, $height);
    }

    public function test_process_throws_validation_exception_for_invalid_step_params(): void
    {
        $service = app(ImagePipelineService::class);

        $sourceRelativePath = 'uploads/pipeline/source-invalid.jpg';
        $destRelativePath = 'results/pipeline/invalid/output.jpg';

        $this->storeFakeImage($sourceRelativePath, 100, 60);

        $this->expectException(ValidationException::class);

        $service->process(
            sourcePath: Storage::disk('local')->path($sourceRelativePath),
            destPath: Storage::disk('local')->path($destRelativePath),
            pipeline: [
                [
                    'step' => 'resize',
                    'params' => ['mode' => 'width'],
                ],
            ],
            outputFormat: 'jpg',
            quality: 85,
        );
    }

    private function storeFakeImage(string $path, int $width, int $height): void
    {
        $file = UploadedFile::fake()->image(basename($path), $width, $height);

        Storage::disk('local')->put($path, file_get_contents($file->getPathname()));
    }
}

