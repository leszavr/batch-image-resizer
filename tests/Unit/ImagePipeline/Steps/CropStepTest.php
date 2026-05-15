<?php

namespace Tests\Unit\ImagePipeline\Steps;

use App\ImagePipeline\Steps\CropStep;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class CropStepTest extends TestCase
{
    public function test_crop_by_position_changes_dimensions(): void
    {
        $step = new CropStep();
        $image = $this->manager()->createImage(1200, 800);

        $result = $step->apply($image, [
            'width' => 400,
            'height' => 300,
            'position' => 'center',
        ]);

        $this->assertSame(400, $result->width());
        $this->assertSame(300, $result->height());
    }

    public function test_crop_by_coordinates_changes_dimensions(): void
    {
        $step = new CropStep();
        $image = $this->manager()->createImage(1200, 800);

        $result = $step->apply($image, [
            'width' => 300,
            'height' => 200,
            'x' => 100,
            'y' => 50,
        ]);

        $this->assertSame(300, $result->width());
        $this->assertSame(200, $result->height());
    }

    public function test_crop_validate_throws_when_dimensions_missing(): void
    {
        $step = new CropStep();

        $this->expectException(ValidationException::class);
        $step->validate(['width' => 300]);
    }

    private function manager(): ImageManager
    {
        return new ImageManager(new GdDriver());
    }
}

