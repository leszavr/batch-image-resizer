<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
                    'name' => __('tools.crop.name'),
                    'description' => __('tools.crop.description'),
                    'icon' => 'crop',
                    'route' => 'tools.crop',
                ],
                [
                    'id' => 'rotate',
                    'name' => __('tools.rotate.name'),
                    'description' => __('tools.rotate.description'),
                    'icon' => 'rotate',
                    'route' => 'tools.rotate',
                ],
                [
                    'id' => 'flip',
                    'name' => __('tools.flip.name'),
                    'description' => __('tools.flip.description'),
                    'icon' => 'flip',
                    'route' => 'tools.flip',
                ],
                [
                    'id' => 'resize',
                    'name' => __('tools.resize.name'),
                    'description' => __('tools.resize.description'),
                    'icon' => 'resize',
                    'route' => 'tools.resize',
                ],
            ],
            'filters' => [
                [
                    'id' => 'filters',
                    'name' => __('tools.filters.name'),
                    'description' => __('tools.filters.description'),
                    'icon' => 'filters',
                    'route' => 'tools.filters',
                ],
                [
                    'id' => 'brightness',
                    'name' => __('tools.brightness.name'),
                    'description' => __('tools.brightness.description'),
                    'icon' => 'brightness',
                    'route' => 'tools.brightness',
                ],
                [
                    'id' => 'contrast',
                    'name' => __('tools.contrast.name'),
                    'description' => __('tools.contrast.description'),
                    'icon' => 'contrast',
                    'route' => 'tools.contrast',
                ],
                [
                    'id' => 'saturation',
                    'name' => __('tools.saturation.name'),
                    'description' => __('tools.saturation.description'),
                    'icon' => 'saturation',
                    'route' => 'tools.saturation',
                ],
                [
                    'id' => 'exposure',
                    'name' => __('tools.exposure.name'),
                    'description' => __('tools.exposure.description'),
                    'icon' => 'exposure',
                    'route' => 'tools.exposure',
                ],
                [
                    'id' => 'temperature',
                    'name' => __('tools.temperature.name'),
                    'description' => __('tools.temperature.description'),
                    'icon' => 'temperature',
                    'route' => 'tools.temperature',
                ],
                [
                    'id' => 'gamma',
                    'name' => __('tools.gamma.name'),
                    'description' => __('tools.gamma.description'),
                    'icon' => 'gamma',
                    'route' => 'tools.gamma',
                ],
                [
                    'id' => 'clarity',
                    'name' => __('tools.clarity.name'),
                    'description' => __('tools.clarity.description'),
                    'icon' => 'clarity',
                    'route' => 'tools.clarity',
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
     * Filters Tool (combined)
     */
    public function filters()
    {
        return view('tools.filters');
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
     * Vibrance Tool
     */
    public function vibrance()
    {
        return view('tools.vibrance');
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

        // Генерируем уникальное имя файла
        $sessionId = Str::uuid()->toString();
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // Сохраняем оригинал во временную директорию
        $tempPath = "tools/{$sessionId}";
        Storage::disk('local')->makeDirectory($tempPath);

        $originalPath = $file->storeAs($tempPath, "original.{$extension}", 'local');

        // Обрабатываем изображение в зависимости от инструмента
        $resultPath = $this->applyTool($tool, storage_path("app/private/{$originalPath}"), $options, $tempPath);

        if (!$resultPath) {
            return response()->json([
                'success' => false,
                'error' => __('tools.errors.processing_failed'),
            ], 500);
        }

        // Генерируем URL для preview
        $originalUrl = route('tools.preview', ['session' => $sessionId, 'file' => 'original', 'ext' => $extension]);
        $resultUrl = route('tools.preview', ['session' => $sessionId, 'file' => 'result', 'ext' => $extension]);

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
    private function applyTool(string $tool, string $sourcePath, array $options, string $tempPath): ?string
    {
        $image = Image::decodePath($sourcePath);
        $resultPath = storage_path("app/private/{$tempPath}/result.png");

        switch ($tool) {
            case 'crop':
                $width = (int) ($options['width'] ?? $image->width());
                $height = (int) ($options['height'] ?? $image->height());
                $x = (int) ($options['x'] ?? 0);
                $y = (int) ($options['y'] ?? 0);
                $image->crop($width, $height, $x, $y);
                break;

            case 'rotate':
                $angle = (float) ($options['angle'] ?? 90);
                $image->rotate($angle);
                break;

            case 'flip':
                $direction = $options['direction'] ?? 'horizontal';
                if ($direction === 'horizontal') {
                    $image->flip('h');
                } else {
                    $image->flip('v');
                }
                break;

            case 'resize':
                $width = (int) ($options['width'] ?? $image->width());
                $height = (int) ($options['height'] ?? null);
                $maintainAspect = (bool) ($options['maintain_aspect'] ?? true);

                if ($maintainAspect) {
                    $image->scaleDown(width: $width);
                } else {
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
                // Saturation not available in Intervention Image v4
                // Skip for now
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
                // Temperature not available in Intervention Image v4
                // Skip for now - would need manual pixel manipulation
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
                    $contrast = $level * 0.3;
                    $brightness = $level > 0 ? -$level * 0.1 : $level * 0.1;
                    $image->contrast((int) $contrast);
                    $image->brightness((int) $brightness);
                }
                break;

            case 'filters':
                // Применяем комбинацию фильтров
                if (isset($options['brightness'])) {
                    $image->brightness((int) $options['brightness']);
                }
                if (isset($options['contrast'])) {
                    $image->contrast((int) $options['contrast']);
                }
                if (isset($options['blur']) && $options['blur'] > 0) {
                    $image->blur((int) $options['blur']);
                }
                if (!empty($options['grayscale'])) {
                    $image->greyscale();
                }
                break;

            default:
                return null;
        }

        $image->save($resultPath);

        return $resultPath;
    }
}