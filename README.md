# Batch Image Resizer (BIR)

Laravel-based web application for:
1. Batch processing pipelines for multiple files.
2. Interactive single-image tools with live preview.
3. Plan-based capabilities, API access, and admin operations.

## What the app does

### Batch processor (main page)
1. Upload multiple files in one job.
2. Build processing pipeline (resize/rotate/flip/crop/filter/watermark).
3. Choose output format and quality.
4. Track async progress and download ZIP result.
5. Save/load user presets.

### Online tools (/tools)
Current tools implemented:
1. Crop Image
2. Rotate Image
3. Flip Image
4. Resize Image
5. Add Watermark on Image (text or logo)
6. Annotate on Image (line, rectangle, arrow)
7. Add Frame on Image (solid/double/dashed)
8. Enlarge Image (upscale)
9. Brightness
10. Contrast
11. Saturation
12. Exposure
13. Temperature
14. Gamma
15. Clarity
16. Blur

All tools use live preview with immediate re-processing on control changes.

### Access model
1. Guest and authenticated job access via owner/session checks.
2. Plan-based capabilities (formats, operations, watermark, API, priority queue).
3. Admin area for jobs, plans, users, statistics, and localization management.

## Tech stack
1. Laravel 11
2. PHP 8.2+
3. MySQL/MariaDB (typical deployment)
4. Queue workers (database/redis)
5. Intervention Image + GD/Imagick backend
6. Blade + Tailwind + Alpine/Vite frontend

## Quick start (local)

1. Install dependencies:
```bash
composer install
npm install
```

2. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

3. Set DB credentials in `.env`, then run:
```bash
php artisan migrate
php artisan db:seed
```

4. Start app:
```bash
php artisan serve
npm run dev
```

5. Run queue worker (required for batch jobs):
```bash
php artisan queue:work --queue=image-processing --tries=3 --timeout=300
```

6. Optional scheduler (cleanup/system tasks):
```bash
php artisan schedule:work
```

## Key configuration

Main app settings are in `config/ipp.php` and related env variables:

```env
IPP_MAX_FILE_SIZE_MB=50
IPP_MAX_FILES_PER_JOB=100
IPP_MAX_FILES_FREE=10
IPP_MAX_FILE_SIZE_FREE_MB=10
IPP_STORAGE_TTL_HOURS=24
IPP_QUEUE=image-processing
IPP_QUEUE_TIMEOUT=300
```

Supported output formats (default):
1. jpg
2. png
3. webp
4. avif
5. gif
6. tiff

## Routes overview

Web:
1. `/` - batch processor
2. `/tools/*` - online image tools
3. `/plans`, `/dashboard`, `/history`, `/admin/*`

API (Sanctum + plan.api_access middleware):
1. `POST /api/jobs`
2. `GET /api/jobs/{imageJob}`
3. `GET /api/jobs/{imageJob}/download`

## Testing

Run all tests:
```bash
php artisan test
```

Tools feature tests only:
```bash
php artisan test --filter=ToolsProcessingTest
```

## Notes

1. Tools and batch pipeline are intentionally separate UX flows.
2. Localization uses `dbt(...)` helper and `lang/*/ui.php` keys.
3. Temporary job artifacts are stored locally and cleaned by TTL logic.
