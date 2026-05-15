<?php

namespace App\ImagePipeline\Steps;

use App\ImagePipeline\Contracts\PipelineStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Rotate step.
 *
 * Params:
 *   angle : 90 | 180 | 270 | -90 (or any float for free rotate)
 *   direction : 'left' | 'right' | 'custom'
 */
class RotateStep implements PipelineStep
{
    public function apply(ImageInterface $image, array $params): ImageInterface
    {
        $direction = $params['direction'] ?? 'custom';
        $angle     = (float) ($params['angle'] ?? 90);

        $degrees = match ($direction) {
            'left'  => 90,
            'right' => -90,
            '180'   => 180,
            default => $angle,
        };

        return $image->rotate($degrees);
    }

    public function validate(array $params): void
    {
        $direction = $params['direction'] ?? 'custom';
        $allowed   = ['left', 'right', '180', 'custom'];
        if (! in_array($direction, $allowed)) {
            throw ValidationException::withMessages(['direction' => 'Invalid rotate direction.']);
        }
    }
}
