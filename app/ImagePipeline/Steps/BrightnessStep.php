<?php

namespace App\ImagePipeline\Steps;

use App\ImagePipeline\Contracts\PipelineStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Brightness step.
 *
 * Params:
 *   level : -100 to 100 (0 = no change, -100 = black, 100 = white)
 */
class BrightnessStep implements PipelineStep
{
    public function apply(ImageInterface $image, array $params): ImageInterface
    {
        $level = (int) ($params['level'] ?? 0);

        return $image->brightness($level);
    }

    public function validate(array $params): void
    {
        $level = $params['level'] ?? 0;

        if (! is_numeric($level) || $level < -100 || $level > 100) {
            throw ValidationException::withMessages(['level' => 'Brightness level must be between -100 and 100.']);
        }
    }
}