<?php

namespace Tests\Unit\ImagePipeline\Steps;

use App\ImagePipeline\Steps\FlipStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class FlipStepTest extends TestCase
{
    public function test_flip_horizontal_returns_image_interface(): void
    {
        $step = new FlipStep();
        $image = $this->manager()->createImage(120, 80);

        $result = $step->apply($image, ['axis' => 'horizontal']);

        $this->assertSame(120, $result->width());
        $this->assertSame(80, $result->height());
    }

    public function test_flip_both_returns_image_interface(): void
    {
        $step = new FlipStep();
        $image = $this->manager()->createImage(120, 80);

        $result = $step->apply($image, ['axis' => 'both']);

        $this->assertSame(120, $result->width());
        $this->assertSame(80, $result->height());
    }

    public function test_flip_validate_throws_on_invalid_axis(): void
    {
        $step = new FlipStep();

        $this->expectException(ValidationException::class);
        $step->validate(['axis' => 'diagonal']);
    }

    private function manager(): ImageManager
    {
        return new ImageManager(new GdDriver());
    }
}

