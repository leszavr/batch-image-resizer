<?php

namespace Tests\Unit\ImagePipeline\Steps;

use App\ImagePipeline\Steps\RotateStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class RotateStepTest extends TestCase
{
    public function test_rotate_left_swaps_dimensions(): void
    {
        $step = new RotateStep();
        $image = $this->manager()->createImage(120, 80);

        $result = $step->apply($image, ['direction' => 'left']);

        $this->assertSame(80, $result->width());
        $this->assertSame(120, $result->height());
    }

    public function test_rotate_180_keeps_dimensions(): void
    {
        $step = new RotateStep();
        $image = $this->manager()->createImage(120, 80);

        $result = $step->apply($image, ['direction' => '180']);

        $this->assertSame(120, $result->width());
        $this->assertSame(80, $result->height());
    }

    public function test_rotate_validate_throws_on_invalid_direction(): void
    {
        $step = new RotateStep();

        $this->expectException(ValidationException::class);
        $step->validate(['direction' => 'up']);
    }

    private function manager(): ImageManager
    {
        return new ImageManager(new GdDriver());
    }
}

