<?php

namespace App\ImagePipeline\Steps;

use App\ImagePipeline\Contracts\PipelineStep;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Watermark step.
 *
 * Params:
 *   text     : string  — watermark text (if no image)
 *   position : 'top-left' | 'top' | 'top-right' | 'left' | 'center' |
 *              'right' | 'bottom-left' | 'bottom' | 'bottom-right'
 *   opacity  : int 0–100
 *   size     : int  font size (default 24)
 *   color    : string hex (default '#ffffff')
 */
class WatermarkStep implements PipelineStep
{
    public function apply(ImageInterface $image, array $params): ImageInterface
    {
        $text     = $params['text']     ?? config('app.name');
        $position = $params['position'] ?? 'bottom-right';
        $opacity  = (int) ($params['opacity'] ?? 50);
        $size     = (int) ($params['size']    ?? 24);
        $color    = $params['color']    ?? '#ffffff';

        // Draw semi-transparent text watermark
        $image->text($text, 0, 0, function ($font) use ($size, $color, $opacity) {
            $font->size($size);
            $font->color($color . dechex((int)($opacity / 100 * 255)));
        });

        return $image;
    }

    public function validate(array $params): void
    {
        // text is optional — defaults to app name
    }
}
