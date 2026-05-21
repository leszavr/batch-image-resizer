<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ToolsProcessingTest extends TestCase
{
    /** @var string[] */
    private array $toolSessions = [];

    /** @var string[] */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->toolSessions as $sessionId) {
            File::deleteDirectory(storage_path("app/private/tools/{$sessionId}"));
        }

        foreach ($this->tempFiles as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }

        parent::tearDown();
    }

    public function test_crop_processing_clamps_invalid_dimensions_and_offsets(): void
    {
        $response = $this->post(route('tools.process'), [
            'tool' => 'crop',
            'image' => UploadedFile::fake()->image('crop-source.jpg', 120, 80),
            'options' => [
                'width' => 0,
                'height' => 0,
                'x' => 999,
                'y' => 999,
            ],
        ]);

        $payload = $response
            ->assertOk()
            ->assertJson(['success' => true])
            ->json();

        $this->rememberSession($payload['session_id']);
        $this->assertProcessedImageSize($payload['session_id'], 1, 1);
        $this->get($payload['result_url'])->assertOk();
    }

    public function test_resize_processing_falls_back_from_zero_dimensions(): void
    {
        $response = $this->post(route('tools.process'), [
            'tool' => 'resize',
            'image' => UploadedFile::fake()->image('resize-source.jpg', 120, 80),
            'options' => [
                'width' => 0,
                'height' => 0,
                'maintain_aspect' => false,
            ],
        ]);

        $payload = $response
            ->assertOk()
            ->assertJson(['success' => true])
            ->json();

        $this->rememberSession($payload['session_id']);
        $this->assertProcessedImageSize($payload['session_id'], 120, 80);
        $this->get($payload['result_url'])->assertOk();
    }

    #[DataProvider('successfulToolsProvider')]
    public function test_all_tools_process_successfully(string $tool, array $options): void
    {
        $payload = $this->processTool($tool, $options);

        $this->assertArrayHasKey('session_id', $payload);
        $this->assertArrayHasKey('result_url', $payload);
        $this->assertArrayHasKey('download_url', $payload);
        $this->get($payload['result_url'])->assertOk();
        $this->get($payload['download_url'])->assertOk();
    }

    public function test_rotate_processing_swaps_image_dimensions(): void
    {
        $payload = $this->processTool('rotate', ['angle' => 90]);

        $this->assertProcessedImageSize($payload['session_id'], 60, 80);
    }

    public function test_enlarge_processing_increases_image_dimensions(): void
    {
        $payload = $this->processTool('enlarge', ['scale' => 150]);

        $this->assertProcessedImageSize($payload['session_id'], 120, 90);
    }

    #[DataProvider('effectToolsProvider')]
    public function test_effect_tools_change_image_pixels(string $tool, array $options): void
    {
        $upload = $this->makePatternUpload();
        $originalPath = $upload->getPathname();

        $payload = $this->processTool($tool, $options, $upload);
        $resultPath = $this->resultPathForSession($payload['session_id']);

        $this->assertImageSignatureChanged($originalPath, $resultPath);
    }

    public static function successfulToolsProvider(): array
    {
        return [
            'crop' => ['crop', ['width' => 48, 'height' => 32, 'x' => 8, 'y' => 6]],
            'rotate' => ['rotate', ['angle' => 90]],
            'flip' => ['flip', ['direction' => 'vertical']],
            'resize' => ['resize', ['width' => 64, 'height' => 40, 'maintain_aspect' => false]],
            'watermark' => ['watermark', ['type' => 'text', 'text' => 'QA', 'size' => 34, 'opacity' => 70, 'position' => 'bottom-right', 'offset_x' => 8, 'offset_y' => 8, 'color' => '#ffffff']],
            'annotate' => ['annotate', ['type' => 'arrow', 'color' => '#ff3b30', 'thickness' => 4, 'opacity' => 100, 'x1' => 10, 'y1' => 20, 'x2' => 80, 'y2' => 80]],
            'frame' => ['frame', ['style' => 'double', 'color' => '#ffffff', 'thickness' => 12]],
            'enlarge' => ['enlarge', ['scale' => 150]],
            'brightness' => ['brightness', ['level' => 40]],
            'contrast' => ['contrast', ['level' => 40]],
            'saturation' => ['saturation', ['level' => 60]],
            'exposure' => ['exposure', ['level' => 45]],
            'temperature' => ['temperature', ['level' => 55]],
            'gamma' => ['gamma', ['level' => 35]],
            'clarity' => ['clarity', ['level' => 60]],
            'blur' => ['blur', ['level' => 8]],
        ];
    }

    public static function effectToolsProvider(): array
    {
        return [
            'flip' => ['flip', ['direction' => 'horizontal']],
            'watermark' => ['watermark', ['type' => 'text', 'text' => 'WM', 'size' => 36, 'opacity' => 70, 'position' => 'top-left', 'offset_x' => 6, 'offset_y' => 6, 'color' => '#ffffff']],
            'annotate' => ['annotate', ['type' => 'line', 'color' => '#00ff00', 'thickness' => 3, 'opacity' => 100, 'x1' => 5, 'y1' => 5, 'x2' => 95, 'y2' => 95]],
            'frame' => ['frame', ['style' => 'solid', 'color' => '#ffffff', 'thickness' => 10]],
            'enlarge' => ['enlarge', ['scale' => 160]],
            'brightness' => ['brightness', ['level' => 40]],
            'contrast' => ['contrast', ['level' => 40]],
            'saturation' => ['saturation', ['level' => 60]],
            'exposure' => ['exposure', ['level' => 45]],
            'temperature' => ['temperature', ['level' => 55]],
            'gamma' => ['gamma', ['level' => 35]],
            'clarity' => ['clarity', ['level' => 60]],
            'blur' => ['blur', ['level' => 8]],
        ];
    }

    public function test_watermark_image_mode_processes_successfully(): void
    {
        $response = $this->post(route('tools.process'), [
            'tool' => 'watermark',
            'image' => $this->makePatternUpload(),
            'watermark_image' => UploadedFile::fake()->image('logo.png', 30, 20),
            'options' => [
                'type' => 'image',
                'position' => 'bottom-right',
                'size' => 30,
                'opacity' => 75,
                'offset_x' => 6,
                'offset_y' => 6,
            ],
        ]);

        $payload = $response
            ->assertOk()
            ->assertJson(['success' => true])
            ->json();

        $this->rememberSession($payload['session_id']);
        $this->get($payload['result_url'])->assertOk();
    }

    private function rememberSession(string $sessionId): void
    {
        $this->toolSessions[] = $sessionId;
    }

    private function processTool(string $tool, array $options, ?UploadedFile $upload = null): array
    {
        $response = $this->post(route('tools.process'), [
            'tool' => $tool,
            'image' => $upload ?? $this->makePatternUpload(),
            'options' => $options,
        ]);

        $payload = $response
            ->assertOk()
            ->assertJson(['success' => true])
            ->json();

        $this->rememberSession($payload['session_id']);

        return $payload;
    }

    private function makePatternUpload(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'tool-pattern-');
        $pngPath = $path . '.png';
        rename($path, $pngPath);

        $image = imagecreatetruecolor(80, 60);

        $background = imagecolorallocate($image, 245, 245, 245);
        $red = imagecolorallocate($image, 220, 60, 60);
        $green = imagecolorallocate($image, 40, 170, 90);
        $blue = imagecolorallocate($image, 60, 110, 220);
        $yellow = imagecolorallocate($image, 240, 210, 60);
        $black = imagecolorallocate($image, 15, 15, 15);
        $white = imagecolorallocate($image, 255, 255, 255);

        imagefilledrectangle($image, 0, 0, 79, 59, $background);
        imagefilledrectangle($image, 0, 0, 39, 29, $red);
        imagefilledrectangle($image, 40, 0, 79, 29, $green);
        imagefilledrectangle($image, 0, 30, 39, 59, $blue);
        imagefilledrectangle($image, 40, 30, 79, 59, $yellow);
        imageline($image, 0, 0, 79, 59, $black);
        imageline($image, 79, 0, 0, 59, $white);
        imageellipse($image, 40, 30, 30, 18, $black);

        imagepng($image, $pngPath);
        imagedestroy($image);

        $this->tempFiles[] = $pngPath;

        return new UploadedFile($pngPath, 'pattern.png', 'image/png', null, true);
    }

    private function assertProcessedImageSize(string $sessionId, int $expectedWidth, int $expectedHeight): void
    {
        $resultPath = $this->resultPathForSession($sessionId);

        $this->assertFileExists($resultPath);

        [$width, $height] = getimagesize($resultPath);

        $this->assertSame($expectedWidth, $width);
        $this->assertSame($expectedHeight, $height);
    }

    private function resultPathForSession(string $sessionId): string
    {
        return storage_path("app/private/tools/{$sessionId}/result.png");
    }

    private function assertImageSignatureChanged(string $originalPath, string $resultPath): void
    {
        $this->assertFileExists($resultPath);

        $this->assertNotSame(
            $this->sampleImageSignature($originalPath),
            $this->sampleImageSignature($resultPath)
        );
    }

    private function sampleImageSignature(string $path): string
    {
        $image = imagecreatefromstring(file_get_contents($path));
        $width = imagesx($image);
        $height = imagesy($image);
        $samples = [];

        for ($y = 0; $y < $height; $y += max(1, (int) floor($height / 6))) {
            for ($x = 0; $x < $width; $x += max(1, (int) floor($width / 6))) {
                $rgb = imagecolorat($image, $x, $y);
                $samples[] = [
                    ($rgb >> 16) & 0xFF,
                    ($rgb >> 8) & 0xFF,
                    $rgb & 0xFF,
                ];
            }
        }

        imagedestroy($image);

        return md5(json_encode([$width, $height, $samples], JSON_THROW_ON_ERROR));
    }
}
