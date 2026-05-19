<?php

namespace App\ImagePipeline;

use App\ImagePipeline\Contracts\PipelineStep;
use App\ImagePipeline\Steps\CropStep;
use App\ImagePipeline\Steps\FilterStep;
use App\ImagePipeline\Steps\FlipStep;
use App\ImagePipeline\Steps\ResizeStep;
use App\ImagePipeline\Steps\RotateStep;
use App\ImagePipeline\Steps\WatermarkStep;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class ImagePipelineService
{
    protected ImageManager $manager;

    /** step_name => class */
    protected array $registry = [
        'resize'    => ResizeStep::class,
        'rotate'    => RotateStep::class,
        'flip'      => FlipStep::class,
        'crop'      => CropStep::class,
        'watermark' => WatermarkStep::class,
        'filter'    => FilterStep::class,
    ];

    public function __construct()
    {
        // Prefer Imagick, fall back to GD
        $this->manager = extension_loaded('imagick')
            ? new ImageManager(new ImagickDriver())
            : new ImageManager(new GdDriver());
    }

    /**
     * Process a source file through a pipeline and save to destination.
     *
     * @param  string  $sourcePath   Absolute path to source file
     * @param  string  $destPath     Absolute path to destination file
     * @param  array   $pipeline     [['step' => 'resize', 'params' => [...]], ...]
     * @param  string  $outputFormat jpg|png|webp|avif|gif|tiff
     * @param  int     $quality      1–100
     * @throws \Throwable
     */
    public function process(
        string $sourcePath,
        string $destPath,
        array  $pipeline,
        string $outputFormat = 'jpg',
        int    $quality = 85
    ): void {
        $image = $this->manager->decodePath($sourcePath);

        foreach ($pipeline as $step) {
            $stepName = $step['step']   ?? null;
            $params   = $step['params'] ?? [];

            if (! $stepName || ! isset($this->registry[$stepName])) {
                Log::warning("ImagePipeline: unknown step '{$stepName}', skipping.");
                continue;
            }

            /** @var PipelineStep $handler */
            $handler = app($this->registry[$stepName]);
            $handler->validate($params);
            $image = $handler->apply($image, $params);
        }

        // Ensure destination directory exists
        $dir = dirname($destPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $saveOptions = match (strtolower($outputFormat)) {
            'jpg', 'jpeg', 'webp', 'avif' => ['quality' => $quality],
            default => [],
        };

        $image->save($destPath, ...$saveOptions);
    }

    /**
     * Register a custom step at runtime.
     */
    public function registerStep(string $name, string $class): void
    {
        $this->registry[$name] = $class;
    }

    public function supportedSteps(): array
    {
        return array_keys($this->registry);
    }
}
