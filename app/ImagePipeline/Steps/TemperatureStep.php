<?php

namespace App\ImagePipeline\Steps;

use App\ImagePipeline\Contracts\PipelineStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Temperature step.
 *
 * Params:
 *   level : -100 to 100 (warm/cool adjustment)
 */
class TemperatureStep implements PipelineStep
{
    public function apply(ImageInterface $image, array $params): ImageInterface
    {
        $level = (int) ($params['level'] ?? 0);

        if ($level > 0) {
            // Warm colors - add yellow/red
            $image = $image->colorize($level, 0, 0);
        } elseif ($level < 0) {
            // Cool colors - add blue
            $image = $image->colorize(0, 0, abs($level));
        }

        return $image;
    }

    public function validate(array $params): void
    {
        $level = $params['level'] ?? 0;

        if (! is_numeric($level) || $level < -100 || $level > 100) {
            throw ValidationException::withMessages(['level' => 'Temperature level must be between -100 and 100.']);
        }
    }
}