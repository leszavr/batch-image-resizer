<?php

namespace App\ImagePipeline\Steps;

use App\ImagePipeline\Contracts\PipelineStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Clarity step - enhances mid-tone contrast.
 *
 * Params:
 *   level : -100 to 100 (clarity adjustment)
 */
class ClarityStep implements PipelineStep
{
    public function apply(ImageInterface $image, array $params): ImageInterface
    {
        $level = (int) ($params['level'] ?? 0);

        if ($level !== 0) {
            // Clarity = contrast + subtle sharpening effect
            $contrast = $level * 0.3;
            $brightness = $level > 0 ? -$level * 0.1 : $level * 0.1;

            $image = $image->contrast((int) $contrast);
            $image = $image->brightness((int) $brightness);
        }

        return $image;
    }

    public function validate(array $params): void
    {
        $level = $params['level'] ?? 0;

        if (! is_numeric($level) || $level < -100 || $level > 100) {
            throw ValidationException::withMessages(['level' => 'Clarity level must be between -100 and 100.']);
        }
    }
}