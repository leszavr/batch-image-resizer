<?php

namespace App\ImagePipeline\Steps;

use App\ImagePipeline\Contracts\PipelineStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Direction;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Flip step.
 *
 * Params:
 *   axis : 'horizontal' | 'vertical' | 'both'
 */
class FlipStep implements PipelineStep
{
    public function apply(ImageInterface $image, array $params): ImageInterface
    {
        $axis = $params['axis'] ?? 'horizontal';

        if ($axis === 'horizontal' || $axis === 'both') {
            $image = $image->flip(Direction::HORIZONTAL);
        }
        if ($axis === 'vertical' || $axis === 'both') {
            $image = $image->flip(Direction::VERTICAL);
        }

        return $image;
    }

    public function validate(array $params): void
    {
        $axis    = $params['axis'] ?? 'horizontal';
        $allowed = ['horizontal', 'vertical', 'both'];
        if (! in_array($axis, $allowed)) {
            throw ValidationException::withMessages(['axis' => 'Invalid flip axis.']);
        }
    }
}
