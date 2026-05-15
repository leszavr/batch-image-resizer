<?php

namespace App\ImagePipeline\Steps;

use App\ImagePipeline\Contracts\PipelineStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Resize step.
 *
 * Params:
 *   mode     : 'fixed' | 'width' | 'height' | 'fit' | 'cover'
 *   width    : int (px) — required for fixed/width/fit/cover
 *   height   : int (px) — required for fixed/height/fit/cover
 *   upscale  : bool     — allow enlarging (default false)
 */
class ResizeStep implements PipelineStep
{
    public function apply(ImageInterface $image, array $params): ImageInterface
    {
        $mode    = $params['mode']   ?? 'fixed';
        $width   = isset($params['width'])  ? (int) $params['width']  : null;
        $height  = isset($params['height']) ? (int) $params['height'] : null;
        $upscale = (bool) ($params['upscale'] ?? false);

        $origW = $image->width();
        $origH = $image->height();

        switch ($mode) {
            case 'width':
                if (! $upscale && $width >= $origW) break;
                $image = $image->scale(width: $width);
                break;

            case 'height':
                if (! $upscale && $height >= $origH) break;
                $image = $image->scale(height: $height);
                break;

            case 'fit':
                $image = $image->contain($width, $height);
                break;

            case 'cover':
                $image = $image->cover($width, $height);
                break;

            case 'fixed':
            default:
                if (! $upscale && $width >= $origW && $height >= $origH) break;
                $image = $image->resize($width, $height);
                break;
        }

        return $image;
    }

    public function validate(array $params): void
    {
        $mode = $params['mode'] ?? 'fixed';
        $allowedModes = ['fixed', 'width', 'height', 'fit', 'cover'];

        if (! in_array($mode, $allowedModes)) {
            throw ValidationException::withMessages(['mode' => 'Invalid resize mode.']);
        }
        if (in_array($mode, ['fixed', 'fit', 'cover', 'width']) && empty($params['width'])) {
            throw ValidationException::withMessages(['width' => 'Width is required for this resize mode.']);
        }
        if (in_array($mode, ['fixed', 'fit', 'cover', 'height']) && empty($params['height'])) {
            throw ValidationException::withMessages(['height' => 'Height is required for this resize mode.']);
        }
    }
}
