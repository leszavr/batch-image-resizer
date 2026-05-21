<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Direction;
use Intervention\Image\Laravel\Facades\Image;

class ToolsController extends Controller
{
    /**
     * Страница списка всех инструментов
     */
    public function index()
    {
        $tools = [
            'basic' => [
                [
                    'id' => 'crop',
                    'name' => 'tools.crop.name',
                    'description' => 'tools.crop.description',
                    'icon' => 'crop',
                    'route' => 'tools.crop',
                ],
                [
                    'id' => 'rotate',
                    'name' => 'tools.rotate.name',
                    'description' => 'tools.rotate.description',
                    'icon' => 'rotate',
                    'route' => 'tools.rotate',
                ],
                [
                    'id' => 'flip',
                    'name' => 'tools.flip.name',
                    'description' => 'tools.flip.description',
                    'icon' => 'flip',
                    'route' => 'tools.flip',
                ],
                [
                    'id' => 'resize',
                    'name' => 'tools.resize.name',
                    'description' => 'tools.resize.description',
                    'icon' => 'resize',
                    'route' => 'tools.resize',
                ],
                [
                    'id' => 'watermark',
                    'name' => 'tools.watermark.name',
                    'description' => 'tools.watermark.description',
                    'icon' => 'watermark',
                    'route' => 'tools.watermark',
                ],
                [
                    'id' => 'annotate',
                    'name' => 'tools.annotate.name',
                    'description' => 'tools.annotate.description',
                    'icon' => 'annotate',
                    'route' => 'tools.annotate',
                ],
                [
                    'id' => 'frame',
                    'name' => 'tools.frame.name',
                    'description' => 'tools.frame.description',
                    'icon' => 'frame',
                    'route' => 'tools.frame',
                ],
                [
                    'id' => 'enlarge',
                    'name' => 'tools.enlarge.name',
                    'description' => 'tools.enlarge.description',
                    'icon' => 'enlarge',
                    'route' => 'tools.enlarge',
                ],
            ],
            'filters' => [
                [
                    'id' => 'brightness',
                    'name' => 'tools.brightness.name',
                    'description' => 'tools.brightness.description',
                    'icon' => 'brightness',
                    'route' => 'tools.brightness',
                ],
                [
                    'id' => 'contrast',
                    'name' => 'tools.contrast.name',
                    'description' => 'tools.contrast.description',
                    'icon' => 'contrast',
                    'route' => 'tools.contrast',
                ],
                [
                    'id' => 'saturation',
                    'name' => 'tools.saturation.name',
                    'description' => 'tools.saturation.description',
                    'icon' => 'saturation',
                    'route' => 'tools.saturation',
                ],
                [
                    'id' => 'exposure',
                    'name' => 'tools.exposure.name',
                    'description' => 'tools.exposure.description',
                    'icon' => 'exposure',
                    'route' => 'tools.exposure',
                ],
                [
                    'id' => 'temperature',
                    'name' => 'tools.temperature.name',
                    'description' => 'tools.temperature.description',
                    'icon' => 'temperature',
                    'route' => 'tools.temperature',
                ],
                [
                    'id' => 'gamma',
                    'name' => 'tools.gamma.name',
                    'description' => 'tools.gamma.description',
                    'icon' => 'gamma',
                    'route' => 'tools.gamma',
                ],
                [
                    'id' => 'clarity',
                    'name' => 'tools.clarity.name',
                    'description' => 'tools.clarity.description',
                    'icon' => 'clarity',
                    'route' => 'tools.clarity',
                ],
                [
                    'id' => 'blur',
                    'name' => 'tools.blur.name',
                    'description' => 'tools.blur.description',
                    'icon' => 'blur',
                    'route' => 'tools.blur',
                ],
            ],
        ];

        return view('tools.index', compact('tools'));
    }

    /**
     * Crop Tool
     */
    public function crop()
    {
        return view('tools.crop');
    }

    /**
     * Rotate Tool
     */
    public function rotate()
    {
        return view('tools.rotate');
    }

    /**
     * Flip Tool
     */
    public function flip()
    {
        return view('tools.flip');
    }

    /**
     * Resize Tool
     */
    public function resize()
    {
        return view('tools.resize');
    }

    /**
     * Watermark Tool
     */
    public function watermark()
    {
        return view('tools.watermark');
    }

    /**
     * Annotate Tool
     */
    public function annotate()
    {
        return view('tools.annotate');
    }

    /**
     * Frame Tool
     */
    public function frame()
    {
        return view('tools.frame');
    }

    /**
     * Enlarge Tool
     */
    public function enlarge()
    {
        return view('tools.enlarge');
    }

    /**
     * Brightness Tool
     */
    public function brightness()
    {
        return view('tools.brightness');
    }

    /**
     * Contrast Tool
     */
    public function contrast()
    {
        return view('tools.contrast');
    }

    /**
     * Saturation Tool
     */
    public function saturation()
    {
        return view('tools.saturation');
    }

    /**
     * Exposure Tool
     */
    public function exposure()
    {
        return view('tools.exposure');
    }

    /**
     * Temperature Tool
     */
    public function temperature()
    {
        return view('tools.temperature');
    }

    /**
     * Gamma Tool
     */
    public function gamma()
    {
        return view('tools.gamma');
    }

    /**
     * Clarity Tool
     */
    public function clarity()
    {
        return view('tools.clarity');
    }

    /**
     * Blur Tool
     */
    public function blur()
    {
        return view('tools.blur');
    }

    /**
     * Process image - общий метод для обработки
     */
    public function process(Request $request)
    {
        $request->validate([
            'tool' => 'required|string',
            'image' => 'required|image|max:10240', // 10MB max
            'options' => 'nullable|array',
        ]);

        $tool = $request->input('tool');
        $file = $request->file('image');
        $options = $request->input('options', []);

        if ($tool === 'watermark' && ($options['type'] ?? 'text') === 'image') {
            $request->validate([
                'watermark_image' => 'required|image|max:5120',
            ]);
        }

        // Генерируем уникальное имя файла
        $sessionId = Str::uuid()->toString();
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // Сохраняем оригинал во временную директорию
        $tempPath = "tools/{$sessionId}";
        Storage::disk('local')->makeDirectory($tempPath);

        $originalPath = $file->storeAs($tempPath, "original.{$extension}", 'local');
        $watermarkImagePath = null;

        if ($tool === 'watermark' && $request->hasFile('watermark_image')) {
            $watermarkFile = $request->file('watermark_image');
            $watermarkExt = strtolower($watermarkFile->getClientOriginalExtension() ?: 'png');
            $stored = $watermarkFile->storeAs($tempPath, "watermark.{$watermarkExt}", 'local');
            $watermarkImagePath = storage_path("app/private/{$stored}");
        }

        try {
            // Обрабатываем изображение в зависимости от инструмента
            $resultPath = $this->applyTool(
                $tool,
                storage_path("app/private/{$originalPath}"),
                $options,
                $tempPath,
                $watermarkImagePath
            );
        } catch (\Throwable $e) {
            Log::warning('Tools image processing failed', [
                'tool' => $tool,
                'userId' => auth()->id(),
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => dbt('tools.errors.processing_failed'),
            ], 422);
        }

        if (!$resultPath) {
            return response()->json([
                'success' => false,
                'error' => dbt('tools.errors.processing_failed'),
            ], 500);
        }

        // Генерируем URL для preview
        $originalUrl = route('tools.preview', ['session' => $sessionId, 'file' => 'original', 'ext' => $extension]);
        $resultExtension = pathinfo($resultPath, PATHINFO_EXTENSION) ?: 'png';
        $resultUrl = route('tools.preview', ['session' => $sessionId, 'file' => 'result', 'ext' => $resultExtension]);

        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'original_url' => $originalUrl,
            'result_url' => $resultUrl,
            'download_url' => route('tools.download', ['session' => $sessionId]),
        ]);
    }

    /**
     * Preview processed image
     */
    public function preview(string $session, string $file, string $ext)
    {
        $path = "tools/{$session}/{$file}.{$ext}";

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $fullPath = storage_path("app/private/{$path}");

        return response()->file($fullPath, [
            'Content-Type' => "image/{$ext}",
            'Cache-Control' => 'no-cache',
        ]);
    }

    /**
     * Download processed image
     */
    public function download(string $session)
    {
        $tempPath = "tools/{$session}";
        $resultPath = storage_path("app/private/{$tempPath}/result.png");

        if (!file_exists($resultPath)) {
            // Пробуем другие расширения
            foreach (['jpg', 'jpeg', 'webp', 'gif'] as $ext) {
                $altPath = storage_path("app/private/{$tempPath}/result.{$ext}");
                if (file_exists($altPath)) {
                    $resultPath = $altPath;
                    break;
                }
            }
        }

        if (!file_exists($resultPath)) {
            abort(404);
        }

        return response()->download($resultPath, 'processed-image.png');
    }

    /**
     * Apply tool processing
     */
    private function applyTool(
        string $tool,
        string $sourcePath,
        array $options,
        string $tempPath,
        ?string $watermarkImagePath = null
    ): ?string
    {
        if ($tool === 'watermark') {
            return $this->applyWatermark($sourcePath, $options, $tempPath, $watermarkImagePath);
        }

        if ($tool === 'annotate') {
            return $this->applyAnnotate($sourcePath, $options, $tempPath);
        }

        if ($tool === 'frame') {
            return $this->applyFrame($sourcePath, $options, $tempPath);
        }

        if ($tool === 'enlarge') {
            return $this->applyEnlarge($sourcePath, $options, $tempPath);
        }

        $image = Image::decodePath($sourcePath);
        $resultPath = storage_path("app/private/{$tempPath}/result.png");
        $sourceWidth = $image->width();
        $sourceHeight = $image->height();

        switch ($tool) {
            case 'crop':
                $width = $this->normalizeDimension($options['width'] ?? null, $sourceWidth, $sourceWidth);
                $height = $this->normalizeDimension($options['height'] ?? null, $sourceHeight, $sourceHeight);
                $x = $this->normalizeOffset($options['x'] ?? null, $sourceWidth - 1);
                $y = $this->normalizeOffset($options['y'] ?? null, $sourceHeight - 1);
                $width = max(1, min($width, $sourceWidth - $x));
                $height = max(1, min($height, $sourceHeight - $y));
                $image->crop($width, $height, $x, $y);
                break;

            case 'rotate':
                $angle = (float) ($options['angle'] ?? 90);
                $image->rotate($angle);
                break;

            case 'flip':
                $direction = $options['direction'] ?? 'horizontal';
                if ($direction === 'horizontal') {
                    $image->flip(Direction::HORIZONTAL);
                } else {
                    $image->flip(Direction::VERTICAL);
                }
                break;

            case 'resize':
                $width = $this->normalizeDimension($options['width'] ?? null, $sourceWidth);
                $height = $this->normalizeNullableDimension($options['height'] ?? null);
                $maintainAspect = (bool) ($options['maintain_aspect'] ?? true);

                if ($maintainAspect) {
                    $image->scaleDown(width: $width);
                } else {
                    $height ??= $sourceHeight;
                    $image->resize($width, $height);
                }
                break;

            case 'brightness':
                $level = (int) ($options['level'] ?? 0);
                $image->brightness($level);
                break;

            case 'contrast':
                $level = (int) ($options['level'] ?? 0);
                $image->contrast($level);
                break;

            case 'saturation':
                $level = (int) ($options['level'] ?? 0);
                $this->applySaturationAdjustment($image, $level);
                break;

            case 'exposure':
                $level = (int) ($options['level'] ?? 0);
                if ($level !== 0) {
                    $brightness = $level * 0.5;
                    $gamma = 1 + ($level / 200);
                    $image->brightness((int) $brightness);
                    $image->gamma($gamma);
                }
                break;

            case 'temperature':
                $level = (int) ($options['level'] ?? 0);
                $this->applyTemperatureAdjustment($image, $level);
                break;

            case 'gamma':
                $level = (int) ($options['level'] ?? 0);
                $gamma = 1 + ($level / -200);
                $gamma = max(0.5, min(2.0, $gamma));
                $image->gamma($gamma);
                break;

            case 'clarity':
                $level = (int) ($options['level'] ?? 0);
                if ($level !== 0) {
                    if ($level > 0) {
                        $image->sharpen(max(1, (int) round($level / 2)));
                        $image->contrast((int) round($level * 0.2));
                    } else {
                        $image->blur(max(1, (int) round(abs($level) / 12)));
                        $image->brightness((int) round($level * 0.1));
                    }
                }
                break;

            case 'blur':
                $level = (int) ($options['level'] ?? 0);
                if ($level > 0) {
                    $image->blur(max(1, min(30, $level)));
                }
                break;

            default:
                return null;
        }

        $image->save($resultPath);

        return $resultPath;
    }

    private function normalizeDimension(mixed $value, int $fallback, ?int $max = null): int
    {
        if (! is_numeric($value) || (int) $value <= 0) {
            return $fallback;
        }

        $dimension = (int) $value;

        if ($max !== null) {
            return max(1, min($dimension, $max));
        }

        return max(1, $dimension);
    }

    private function normalizeNullableDimension(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value) || (int) $value <= 0) {
            return null;
        }

        return (int) $value;
    }

    private function normalizeOffset(mixed $value, int $max): int
    {
        if (! is_numeric($value)) {
            return 0;
        }

        return max(0, min((int) $value, $max));
    }

    private function applySaturationAdjustment(mixed $image, int $level): void
    {
        if ($level === 0) {
            return;
        }

        if ($level < 0) {
            $image->grayscale();

            if ($level > -100) {
                $image->contrast((int) round($level * 0.2));
                $image->brightness((int) round(abs($level) * 0.05));
            }

            return;
        }

        $boost = max(0, min(100, (int) round($level * 0.35)));
        $image->contrast((int) round($level * 0.15));
        $image->colorize($boost, 0, -$boost);
    }

    private function applyTemperatureAdjustment(mixed $image, int $level): void
    {
        if ($level === 0) {
            return;
        }

        $intensity = max(0, min(100, (int) round(abs($level) * 0.7)));

        if ($level > 0) {
            $image->colorize($intensity, (int) round($intensity * 0.35), -$intensity);
        } else {
            $image->colorize(-$intensity, 0, $intensity);
        }
    }

    private function applyWatermark(string $sourcePath, array $options, string $tempPath, ?string $watermarkImagePath): string
    {
        $base = imagecreatefromstring((string) file_get_contents($sourcePath));
        if (! $base) {
            throw new \RuntimeException('Failed to load source image');
        }

        imagealphablending($base, true);
        imagesavealpha($base, true);

        $baseWidth = imagesx($base);
        $baseHeight = imagesy($base);

        $type = ($options['type'] ?? 'text') === 'image' ? 'image' : 'text';
        $position = (string) ($options['position'] ?? 'bottom-right');
        $offsetX = max(0, (int) ($options['offset_x'] ?? 20));
        $offsetY = max(0, (int) ($options['offset_y'] ?? 20));
        $opacity = max(0, min(100, (int) ($options['opacity'] ?? 60)));

        if ($type === 'image') {
            if (! $watermarkImagePath || ! is_file($watermarkImagePath)) {
                throw new \InvalidArgumentException('Watermark image is required');
            }

            $wm = imagecreatefromstring((string) file_get_contents($watermarkImagePath));
            if (! $wm) {
                throw new \RuntimeException('Failed to load watermark image');
            }

            imagealphablending($wm, true);
            imagesavealpha($wm, true);

            $scale = max(5, min(100, (int) ($options['size'] ?? 25)));
            $targetW = max(1, (int) round($baseWidth * ($scale / 100)));
            $targetH = max(1, (int) round(imagesy($wm) * ($targetW / max(1, imagesx($wm)))));

            $overlay = imagecreatetruecolor($targetW, $targetH);
            imagealphablending($overlay, false);
            imagesavealpha($overlay, true);
            $transparent = imagecolorallocatealpha($overlay, 0, 0, 0, 127);
            imagefill($overlay, 0, 0, $transparent);
            imagecopyresampled($overlay, $wm, 0, 0, 0, 0, $targetW, $targetH, imagesx($wm), imagesy($wm));

            [$x, $y] = $this->resolveWatermarkPosition($position, $baseWidth, $baseHeight, $targetW, $targetH, $offsetX, $offsetY);
            imagecopymerge($base, $overlay, $x, $y, 0, 0, $targetW, $targetH, $opacity);

            imagedestroy($overlay);
            imagedestroy($wm);
        } else {
            $text = trim((string) ($options['text'] ?? 'Watermark'));
            if ($text === '') {
                $text = 'Watermark';
            }

            $size = max(10, min(72, (int) ($options['size'] ?? 28)));
            $font = $size >= 52 ? 5 : ($size >= 38 ? 4 : ($size >= 26 ? 3 : ($size >= 16 ? 2 : 1)));
            $textW = imagefontwidth($font) * strlen($text);
            $textH = imagefontheight($font);

            [$x, $y] = $this->resolveWatermarkPosition($position, $baseWidth, $baseHeight, $textW, $textH, $offsetX, $offsetY);
            $hex = (string) ($options['color'] ?? '#FFFFFF');
            [$r, $g, $b] = $this->parseHexColor($hex);
            $alpha = 127 - (int) round(($opacity / 100) * 127);
            $color = imagecolorallocatealpha($base, $r, $g, $b, max(0, min(127, $alpha)));
            imagestring($base, $font, $x, $y, $text, $color);
        }

        $resultPath = storage_path("app/private/{$tempPath}/result.png");
        imagepng($base, $resultPath);
        imagedestroy($base);

        return $resultPath;
    }

    private function resolveWatermarkPosition(
        string $position,
        int $baseWidth,
        int $baseHeight,
        int $itemWidth,
        int $itemHeight,
        int $offsetX,
        int $offsetY
    ): array {
        $x = $offsetX;
        $y = $offsetY;

        switch ($position) {
            case 'top-right':
                $x = $baseWidth - $itemWidth - $offsetX;
                $y = $offsetY;
                break;
            case 'bottom-left':
                $x = $offsetX;
                $y = $baseHeight - $itemHeight - $offsetY;
                break;
            case 'bottom-right':
                $x = $baseWidth - $itemWidth - $offsetX;
                $y = $baseHeight - $itemHeight - $offsetY;
                break;
            case 'center':
                $x = (int) round(($baseWidth - $itemWidth) / 2);
                $y = (int) round(($baseHeight - $itemHeight) / 2);
                break;
            case 'top-left':
            default:
                $x = $offsetX;
                $y = $offsetY;
                break;
        }

        $x = max(0, min($x, max(0, $baseWidth - $itemWidth)));
        $y = max(0, min($y, max(0, $baseHeight - $itemHeight)));

        return [$x, $y];
    }

    private function parseHexColor(string $hex): array
    {
        $value = ltrim(trim($hex), '#');

        if (strlen($value) === 3) {
            $value = $value[0] . $value[0] . $value[1] . $value[1] . $value[2] . $value[2];
        }

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $value)) {
            return [255, 255, 255];
        }

        return [
            hexdec(substr($value, 0, 2)),
            hexdec(substr($value, 2, 2)),
            hexdec(substr($value, 4, 2)),
        ];
    }

    private function applyAnnotate(string $sourcePath, array $options, string $tempPath): string
    {
        $image = imagecreatefromstring((string) file_get_contents($sourcePath));
        if (! $image) {
            throw new \RuntimeException('Failed to load source image');
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        $width = imagesx($image);
        $height = imagesy($image);

        $type = (string) ($options['type'] ?? 'line');
        $thickness = max(1, min(30, (int) ($options['thickness'] ?? 4)));
        [$r, $g, $b] = $this->parseHexColor((string) ($options['color'] ?? '#ff3b30'));
        $alpha = 127 - (int) round((max(0, min(100, (int) ($options['opacity'] ?? 100))) / 100) * 127);
        $color = imagecolorallocatealpha($image, $r, $g, $b, max(0, min(127, $alpha)));

        $x1 = (int) round($width * (max(0, min(100, (int) ($options['x1'] ?? 15))) / 100));
        $y1 = (int) round($height * (max(0, min(100, (int) ($options['y1'] ?? 15))) / 100));
        $x2 = (int) round($width * (max(0, min(100, (int) ($options['x2'] ?? 85))) / 100));
        $y2 = (int) round($height * (max(0, min(100, (int) ($options['y2'] ?? 85))) / 100));

        imagesetthickness($image, $thickness);

        switch ($type) {
            case 'rectangle':
                imagerectangle($image, min($x1, $x2), min($y1, $y2), max($x1, $x2), max($y1, $y2), $color);
                break;
            case 'arrow':
                $this->drawArrow($image, $x1, $y1, $x2, $y2, $color, $thickness);
                break;
            case 'line':
            default:
                imageline($image, $x1, $y1, $x2, $y2, $color);
                break;
        }

        $resultPath = storage_path("app/private/{$tempPath}/result.png");
        imagepng($image, $resultPath);
        imagedestroy($image);

        return $resultPath;
    }

    private function applyFrame(string $sourcePath, array $options, string $tempPath): string
    {
        $image = imagecreatefromstring((string) file_get_contents($sourcePath));
        if (! $image) {
            throw new \RuntimeException('Failed to load source image');
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        $width = imagesx($image);
        $height = imagesy($image);
        $thickness = max(1, min(200, (int) ($options['thickness'] ?? 20)));
        $style = (string) ($options['style'] ?? 'solid');
        [$r, $g, $b] = $this->parseHexColor((string) ($options['color'] ?? '#ffffff'));
        $color = imagecolorallocate($image, $r, $g, $b);

        for ($i = 0; $i < $thickness; $i++) {
            if ($style === 'dashed' && $i % 2 === 1) {
                continue;
            }

            imagerectangle($image, $i, $i, $width - 1 - $i, $height - 1 - $i, $color);
        }

        if ($style === 'double') {
            $innerOffset = min((int) round($thickness * 1.8), (int) floor(min($width, $height) / 3));
            for ($i = 0; $i < max(1, (int) round($thickness / 2)); $i++) {
                imagerectangle(
                    $image,
                    $innerOffset + $i,
                    $innerOffset + $i,
                    $width - 1 - $innerOffset - $i,
                    $height - 1 - $innerOffset - $i,
                    $color
                );
            }
        }

        $resultPath = storage_path("app/private/{$tempPath}/result.png");
        imagepng($image, $resultPath);
        imagedestroy($image);

        return $resultPath;
    }

    private function applyEnlarge(string $sourcePath, array $options, string $tempPath): string
    {
        $source = imagecreatefromstring((string) file_get_contents($sourcePath));
        if (! $source) {
            throw new \RuntimeException('Failed to load source image');
        }

        imagealphablending($source, true);
        imagesavealpha($source, true);

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        $scale = max(100, min(400, (int) ($options['scale'] ?? 150)));
        $targetWidth = max(1, (int) round($sourceWidth * ($scale / 100)));
        $targetHeight = max(1, (int) round($sourceHeight * ($scale / 100)));

        $result = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($result, false);
        imagesavealpha($result, true);
        $transparent = imagecolorallocatealpha($result, 0, 0, 0, 127);
        imagefill($result, 0, 0, $transparent);

        imagecopyresampled(
            $result,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight
        );

        $resultPath = storage_path("app/private/{$tempPath}/result.png");
        imagepng($result, $resultPath);

        imagedestroy($source);
        imagedestroy($result);

        return $resultPath;
    }

    private function drawArrow(mixed $image, int $x1, int $y1, int $x2, int $y2, int $color, int $thickness): void
    {
        imageline($image, $x1, $y1, $x2, $y2, $color);

        $arrowLength = max(8, $thickness * 4);
        $angle = atan2($y2 - $y1, $x2 - $x1);
        $a1 = $angle + deg2rad(155);
        $a2 = $angle - deg2rad(155);

        $x3 = (int) round($x2 + $arrowLength * cos($a1));
        $y3 = (int) round($y2 + $arrowLength * sin($a1));
        $x4 = (int) round($x2 + $arrowLength * cos($a2));
        $y4 = (int) round($y2 + $arrowLength * sin($a2));

        imageline($image, $x2, $y2, $x3, $y3, $color);
        imageline($image, $x2, $y2, $x4, $y4, $color);
    }

}