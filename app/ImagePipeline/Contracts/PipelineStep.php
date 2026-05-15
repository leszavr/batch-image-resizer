<?php

namespace App\ImagePipeline\Contracts;

use Intervention\Image\Interfaces\ImageInterface;

interface PipelineStep
{
    /**
     * Apply this step to the image.
     *
     * @param  ImageInterface  $image
     * @param  array           $params  Step-specific parameters
     * @return ImageInterface
     */
    public function apply(ImageInterface $image, array $params): ImageInterface;

    /**
     * Validate step parameters. Throw ValidationException on failure.
     */
    public function validate(array $params): void;
}
