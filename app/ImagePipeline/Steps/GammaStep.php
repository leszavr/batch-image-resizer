<?php

namespace App\ImagePipeline\Steps;

use App\ImagePipeline\Contracts\PipelineStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Gamma step.
 *
 * Params:
 *   level : -100 to 100 (gamma correction)
 */
class GammaStep implements PipelineStep
{
    public function apply(ImageInterface $image, array $params): ImageInterface
    {
        $level = (int) ($params['level'] ?? 0);

        // Convert level to gamma value
        // 0 -> 1.0 (no change)
        // -100 -> 2.0 (darker)
        // 100 -> 0.5 (lighter)
        $gamma = 1 + ($level / -200);

        // Clamp to reasonable range
        $gamma = max(0.5, min(2.0, $gamma));

        return $image->gamma($gamma);
    }

    public function validate(array $params): void
    {
        $level = $params['level'] ?? 0;

        if (! is_numeric($level) || $level < -100 || $level > 100) {
            throw ValidationException::withMessages(['level' => 'Gamma level must be between -100 and 100.']);
        }
    }
}