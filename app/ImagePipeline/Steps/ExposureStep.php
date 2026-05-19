<?php

namespace App\ImagePipeline\Steps;

use App\ImagePipeline\Contracts\PipelineStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Exposure step.
 *
 * Params:
 *   level : -100 to 100 (adjust image exposure)
 */
class ExposureStep implements PipelineStep
{
    public function apply(ImageInterface $image, array $params): ImageInterface
    {
        $level = (int) ($params['level'] ?? 0);

        // Exposure affects both brightness and gamma
        if ($level !== 0) {
            $brightness = $level * 0.5; // Scale to reasonable range
            $gamma = 1 + ($level / 200); // Subtle gamma adjustment

            $image = $image->brightness((int) $brightness);
            $image = $image->gamma($gamma);
        }

        return $image;
    }

    public function validate(array $params): void
    {
        $level = $params['level'] ?? 0;

        if (! is_numeric($level) || $level < -100 || $level > 100) {
            throw ValidationException::withMessages(['level' => 'Exposure level must be between -100 and 100.']);
        }
    }
}