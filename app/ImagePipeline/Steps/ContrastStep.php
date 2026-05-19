<?php

namespace App\ImagePipeline\Steps;

use App\ImagePipeline\Contracts\PipelineStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Contrast step.
 *
 * Params:
 *   level : -100 to 100 (0 = no change, -100 = gray, 100 = max contrast)
 */
class ContrastStep implements PipelineStep
{
    public function apply(ImageInterface $image, array $params): ImageInterface
    {
        $level = (int) ($params['level'] ?? 0);

        return $image->contrast($level);
    }

    public function validate(array $params): void
    {
        $level = $params['level'] ?? 0;

        if (! is_numeric($level) || $level < -100 || $level > 100) {
            throw ValidationException::withMessages(['level' => 'Contrast level must be between -100 and 100.']);
        }
    }
}