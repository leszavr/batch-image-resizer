<?php

namespace Tests\Unit\ImagePipeline\Steps;

use App\ImagePipeline\Steps\ResizeStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class ResizeStepTest extends TestCase
{
    public function test_resize_width_mode_changes_only_width_with_ratio(): void
    {
        $step = new ResizeStep();
        $image = $this->manager()->createImage(1200, 800);

        $result = $step->apply($image, [
            'mode' => 'width',
            'width' => 600,
            'upscale' => true,
        ]);

        $this->assertSame(600, $result->width());
        $this->assertSame(400, $result->height());
    }

    public function test_resize_width_mode_does_not_upscale_when_disabled(): void
    {
        $step = new ResizeStep();
        $image = $this->manager()->createImage(500, 300);

        $result = $step->apply($image, [
            'mode' => 'width',
            'width' => 900,
            'upscale' => false,
        ]);

        $this->assertSame(500, $result->width());
        $this->assertSame(300, $result->height());
    }

    public function test_resize_fixed_mode_changes_dimensions(): void
    {
        $step = new ResizeStep();
        $image = $this->manager()->createImage(1200, 800);

        $result = $step->apply($image, [
            'mode' => 'fixed',
            'width' => 300,
            'height' => 200,
            'upscale' => true,
        ]);

        $this->assertSame(300, $result->width());
        $this->assertSame(200, $result->height());
    }

    public function test_resize_cover_mode_changes_dimensions_exactly(): void
    {
        $step = new ResizeStep();
        $image = $this->manager()->createImage(1200, 800);

        $result = $step->apply($image, [
            'mode' => 'cover',
            'width' => 250,
            'height' => 250,
        ]);

        $this->assertSame(250, $result->width());
        $this->assertSame(250, $result->height());
    }

    public function test_resize_validate_throws_on_invalid_mode(): void
    {
        $step = new ResizeStep();

        $this->expectException(ValidationException::class);
        $step->validate(['mode' => 'invalid-mode']);
    }

    public function test_resize_validate_throws_when_required_dimensions_missing(): void
    {
        $step = new ResizeStep();

        $this->expectException(ValidationException::class);
        $step->validate(['mode' => 'fixed', 'width' => 100]);
    }

    private function manager(): ImageManager
    {
        return new ImageManager(new GdDriver());
    }
}

