<?php

namespace App\ImagePipeline\Steps;

use App\ImagePipeline\Contracts\PipelineStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Filter step - apply image filters (brightness, contrast, saturation, blur, sepia, grayscale, hue rotate).
 *
 * Params:
 *   brightness  : int (-100 to 100) - default 0
 *   contrast    : int (-100 to 100) - default 0
 *   saturation  : int (0 to 200)    - default 100
 *   blur        : int (0 to 20)     - default 0
 *   sepia       : int (0 to 100)    - default 0
 *   grayscale   : int (0 to 100)    - default 0
 *   hue_rotate  : int (0 to 360)    - default 0
 */
class FilterStep implements PipelineStep
{
    public function apply(ImageInterface $image, array $params): ImageInterface
    {
        $brightness = (int) ($params['brightness'] ?? 0);
        $contrast = (int) ($params['contrast'] ?? 0);
        $saturation = (int) ($params['saturation'] ?? 100);
        $blur = (int) ($params['blur'] ?? 0);
        $sepia = (int) ($params['sepia'] ?? 0);
        $grayscale = (int) ($params['grayscale'] ?? 0);
        $hueRotate = (int) ($params['hue_rotate'] ?? 0);

        // Apply hue rotation first (if needed)
        if ($hueRotate !== 0) {
            $image = $this->applyHueRotate($image, $hueRotate);
        }

        // Apply brightness
        if ($brightness !== 0) {
            $image = $image->brightness($brightness);
        }

        // Apply contrast
        if ($contrast !== 0) {
            $image = $image->contrast($contrast);
        }

        // Apply saturation using modulate (brightness 100 = no change, saturation 0-200)
        if ($saturation !== 100) {
            $saturationValue = max(0, min(200, $saturation));
            // Convert 0-200 range to percentage for modulate
            $image = $image->modulate(brightness: 100, saturation: $saturationValue);
        }

        // Apply blur
        if ($blur > 0) {
            $blurAmount = min(20, max(0, $blur));
            $image = $image->blur($blurAmount);
        }

        // Apply sepia effect
        if ($sepia > 0) {
            $image = $this->applySepia($image, $sepia);
        }

        // Apply grayscale
        if ($grayscale > 0) {
            $image = $this->applyGrayscale($image, $grayscale);
        }

        return $image;
    }

    /**
     * Apply hue rotation to image.
     */
    protected function applyHueRotate(ImageInterface $image, int $degrees): ImageInterface
    {
        $degrees = $degrees % 360;
        if ($degrees === 0) {
            return $image;
        }

        $driver = $image->driver();

        // Use Imagick for hue rotation if available
        if ($driver instanceof \Intervention\Image\Drivers\Imagick\Driver) {
            $core = $image->core()->native();
            $core->modulateImage(100, 100, $degrees);
            return $image;
        }

        // Fallback to GD - use HSL color space conversion
        return $this->applyHueRotateGd($image, $degrees);
    }

    /**
     * Apply sepia effect to image.
     */
    protected function applySepia(ImageInterface $image, int $percent): ImageInterface
    {
        $percent = min(100, max(0, $percent));
        if ($percent === 0) {
            return $image;
        }

        $driver = $image->driver();

        // Use Imagick for better sepia if available
        if ($driver instanceof \Intervention\Image\Drivers\Imagick\Driver) {
            $core = $image->core()->native();
            // Sepia tone formula from Imagick
            $core->sepiaToneImage($percent * 2.55); // Convert 0-100 to 0-255 threshold
            return $image;
        }

        // Fallback: colorize towards sepia tones
        $intensity = $percent / 100;
        $redShift = (int) (112 * $intensity);
        $greenShift = (int) (66 * $intensity);
        $blueShift = (int) (20 * $intensity);

        return $image->colorize(red: $redShift, green: $greenShift, blue: $blueShift);
    }

    /**
     * Apply grayscale with intensity control.
     */
    protected function applyGrayscale(ImageInterface $image, int $percent): ImageInterface
    {
        $percent = min(100, max(0, $percent));
        
        if ($percent === 0) {
            return $image;
        }

        if ($percent === 100) {
            return $image->greyscale();
        }

        // For partial grayscale, we blend the grayscale version with original
        $driver = $image->driver();

        if ($driver instanceof \Intervention\Image\Drivers\Imagick\Driver) {
            $core = $image->core()->native();
            // Use modulate with 0 saturation for partial effect
            // This is a simplification - full implementation would require blending
            $core->modulateImage(100, 100 - $percent);
            return $image;
        }

        // For GD: apply partial desaturation via modulate
        $desaturation = 100 - $percent;
        return $image->modulate(brightness: 100, saturation: $desaturation);
    }

    /**
     * Apply hue rotation using GD color manipulation.
     */
    protected function applyHueRotateGd(ImageInterface $image, int $degrees): ImageInterface
    {
        // GD doesn't have native hue rotation, use color matrix approximation
        // This is a simplified implementation
        $radians = deg2rad($degrees);
        $cos = cos($radians);
        $sin = sin($radians);

        // Build color matrix for hue rotation
        $matrix = [
            [0.299 + 0.701 * $cos + 0.168 * $sin, 0.587 - 0.587 * $cos + 0.330 * $sin, 0.114 - 0.114 * $cos - 0.497 * $sin],
            [0.299 - 0.299 * $cos - 0.328 * $sin, 0.587 + 0.413 * $cos + 0.035 * $sin, 0.114 - 0.114 * $cos + 0.292 * $sin],
            [0.299 - 0.300 * $cos + 1.250 * $sin, 0.587 - 0.588 * $cos - 1.050 * $sin, 0.114 + 0.886 * $cos - 0.200 * $sin],
        ];

        // Apply color matrix if the driver supports it
        if (method_exists($image, 'colorMatrix')) {
            return $image->colorMatrix($matrix);
        }

        // Fallback: return image unchanged (hue rotation requires advanced processing)
        return $image;
    }

    public function validate(array $params): void
    {
        $errors = [];
        $prefix = 'jobs.messages.filter_validation.';

        if (isset($params['brightness']) && ($params['brightness'] < -100 || $params['brightness'] > 100)) {
            $errors['brightness'] = dbt($prefix.'brightness');
        }

        if (isset($params['contrast']) && ($params['contrast'] < -100 || $params['contrast'] > 100)) {
            $errors['contrast'] = dbt($prefix.'contrast');
        }

        if (isset($params['saturation']) && ($params['saturation'] < 0 || $params['saturation'] > 200)) {
            $errors['saturation'] = dbt($prefix.'saturation');
        }

        if (isset($params['blur']) && ($params['blur'] < 0 || $params['blur'] > 20)) {
            $errors['blur'] = dbt($prefix.'blur');
        }

        if (isset($params['sepia']) && ($params['sepia'] < 0 || $params['sepia'] > 100)) {
            $errors['sepia'] = dbt($prefix.'sepia');
        }

        if (isset($params['grayscale']) && ($params['grayscale'] < 0 || $params['grayscale'] > 100)) {
            $errors['grayscale'] = dbt($prefix.'grayscale');
        }

        if (isset($params['hue_rotate']) && ($params['hue_rotate'] < 0 || $params['hue_rotate'] > 360)) {
            $errors['hue_rotate'] = dbt($prefix.'hue_rotate');
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}