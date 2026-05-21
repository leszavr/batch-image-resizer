# Batch Image Resizer (BIR)

Laravel-приложение для:
1. Пакетной обработки изображений (batch pipeline).
2. Интерактивных инструментов для одного изображения с live preview.
3. Тарифных ограничений, API-доступа и админ-операций.

## Что умеет приложение

### Batch-процессор (главная страница)
1. Загрузка нескольких файлов в одно задание.
2. Сборка пайплайна обработки (resize/rotate/flip/crop/filter/watermark).
3. Выбор выходного формата и качества.
4. Асинхронное отслеживание прогресса и скачивание ZIP-результата.
5. Сохранение/использование пресетов.

### Онлайн-инструменты (/tools)
Сейчас реализованы:
1. Crop Image
2. Rotate Image
3. Flip Image
4. Resize Image
5. Add Watermark on Image (текст или логотип)
6. Annotate on Image (линия, прямоугольник, стрелка)
7. Add Frame on Image (сплошная/двойная/пунктирная рамка)
8. Enlarge Image (апскейл)
9. Brightness
10. Contrast
11. Saturation
12. Exposure
13. Temperature
14. Gamma
15. Clarity
16. Blur

Все инструменты работают с live preview и повторной обработкой при изменении контролов.

### Модель доступа
1. Доступ к заданиям по владельцу или по сессии.
2. Тарифные возможности: форматы, операции, watermark, API, приоритетная очередь.
3. Админ-зона: задания, планы, пользователи, статистика, локализация.

## Технологии
1. Laravel 11
2. PHP 8.2+
3. MySQL/MariaDB
4. Очереди (database/redis)
5. Intervention Image + GD/Imagick
6. Blade + Tailwind + Alpine/Vite

## Быстрый старт (локально)

1. Установка зависимостей:
```bash
composer install
npm install
```

2. Настройка окружения:
```bash
cp .env.example .env
php artisan key:generate
```

3. Настройте БД в `.env`, затем:
```bash
php artisan migrate
php artisan db:seed
```

4. Запуск приложения:
```bash
php artisan serve
npm run dev
```

5. Запуск воркера очереди (обязательно для batch):
```bash
php artisan queue:work --queue=image-processing --tries=3 --timeout=300
```

6. Планировщик (опционально):
```bash
php artisan schedule:work
```

## Ключевая конфигурация

Основные параметры в `config/ipp.php` и `.env`:

```env
IPP_MAX_FILE_SIZE_MB=50
IPP_MAX_FILES_PER_JOB=100
IPP_MAX_FILES_FREE=10
IPP_MAX_FILE_SIZE_FREE_MB=10
IPP_STORAGE_TTL_HOURS=24
IPP_QUEUE=image-processing
IPP_QUEUE_TIMEOUT=300
```

Поддерживаемые выходные форматы (по умолчанию):
1. jpg
2. png
3. webp
4. avif
5. gif
6. tiff

## Основные роуты

Web:
1. `/` - batch-процессор
2. `/tools/*` - инструменты редактирования
3. `/plans`, `/dashboard`, `/history`, `/admin/*`

API (Sanctum + middleware `plan.api_access`):
1. `POST /api/jobs`
2. `GET /api/jobs/{imageJob}`
3. `GET /api/jobs/{imageJob}/download`

## Тестирование

Все тесты:
```bash
php artisan test
```

Только тесты инструментов:
```bash
php artisan test --filter=ToolsProcessingTest
```

## Примечания

1. Batch-процессор и `/tools` - разные сценарии использования.
2. Локализация работает через `dbt(...)` и ключи в `lang/*/ui.php`.
3. Временные артефакты задач хранятся локально и удаляются по TTL.
