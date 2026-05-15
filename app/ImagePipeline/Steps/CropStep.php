<?php

namespace App\ImagePipeline\Steps;

use App\ImagePipeline\Contracts\PipelineStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Alignment;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Crop step.
 *
 * Params:
 *   width    : int
 *   height   : int
 *   x        : int (offset from left, default 0)
 *   y        : int (offset from top, default 0)
 *   position : 'top-left' | 'top' | 'top-right' | 'left' | 'center' |
 *              'right' | 'bottom-left' | 'bottom' | 'bottom-right'
 *              (used only when x/y not provided)
 */
class CropStep implements PipelineStep
{
    public function apply(ImageInterface $image, array $params): ImageInterface
    {
        $width    = (int) ($params['width']  ?? $image->width());
        $height   = (int) ($params['height'] ?? $image->height());
        $x        = isset($params['x']) ? (int) $params['x'] : null;
        $y        = isset($params['y']) ? (int) $params['y'] : null;
        $position = $params['position'] ?? 'center';

        if ($x !== null && $y !== null) {
            return $image->crop($width, $height, $x, $y);
        }

        return $image->crop($width, $height, alignment: $this->resolveAlignment($position));
    }

    public function validate(array $params): void
    {
        if (empty($params['width']) || empty($params['height'])) {
            throw ValidationException::withMessages(['crop' => 'Width and height are required for crop.']);
        }
    }

    private function resolveAlignment(string $position): Alignment
    {
        return match ($position) {
            'top-left' => Alignment::TOP_LEFT,
            'top' => Alignment::TOP,
            'top-right' => Alignment::TOP_RIGHT,
            'left' => Alignment::LEFT,
            'center' => Alignment::CENTER,
            'right' => Alignment::RIGHT,
            'bottom-left' => Alignment::BOTTOM_LEFT,
            'bottom' => Alignment::BOTTOM,
            'bottom-right' => Alignment::BOTTOM_RIGHT,
            default => Alignment::CENTER,
        };
    }
}
