# Image Processing Platform (IPP)

Мощная веб-платформа для пакетной обработки изображений с поддержкой изменения размеров, конвертации, обрезки, поворота, зеркалирования и AI-улучшений.

## 🚀 Возможности

### Основная обработка изображений
- **Пакетная обработка**: Обрабатывайте несколько изображений одновременно
- **Конвертация форматов**: JPG, PNG, WebP, AVIF, GIF, TIFF
- **Изменение размеров**: Fit, Cover, Фиксированные размеры, По ширине/высоте
- **Обрезка (Crop)**: Обрезка по координатам с контролем позиции
- **Поворот**: 90°, 180°, 270° с автоматической обработкой размеров
- **Отражение**: Горизонтальное и вертикальное зеркалирование
- **Водяной знак**: Добавление текстовых/графических водяных знаков (зависит от тарифа)

### Расширенные функции
- **Система очередей**: Фоновая обработка с Laravel queues
- **Приоритетная обработка**: Премиум тарифы получают выделенную очередь
- **Мультиязычность**: Управление локализацией через базу данных
- **Тарифные планы**: Ограничения функций на основе подписки
- **Автоудаление файлов**: Автоматическая очистка через настраиваемый TTL
- **API доступ**: RESTful API для программной обработки

### Панель администратора
- **Управление пользователями**: Ролевой доступ (User, Admin, Superadmin)
- **Управление тарифами**: Гибкие ценовые планы с настраиваемыми функциями
- **Мониторинг задач**: Отслеживание статуса в реальном времени
- **Аналитика**: Статистика использования и отслеживание доходов
- **Локализация**: Встроенная система управления переводами

## 🛠 Системные требования

### Минимальные
- PHP 8.2+
- MySQL 8.0+ или MariaDB 10.5+
- Composer 2.0+
- Node.js 18+ и npm
- 2GB RAM
- 10GB дискового пространства

### Рекомендуемые
- PHP 8.3+
- MySQL 8.0+ или PostgreSQL
- Redis для управления очередями
- 4GB+ RAM
- 50GB+ SSD хранилище
- CDN для статических ресурсов

### Требуемые PHP расширения
- mysqli или pdo_mysql
- gd или imagick (imagick рекомендуется)
- fileinfo
- mbstring
- openssl
- json
- curl
- zip

## 📦 Установка

### Способ 1: Ручная установка

1. **Клонируйте репозиторий**
   ```bash
   git clone https://github.com/yourorg/ipp.git
   cd ipp
   ```

2. **Установите зависимости**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   ```

3. **Создайте файл окружения**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Настройте базу данных** в `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ipp
   DB_USERNAME=ipp_user
   DB_PASSWORD=your_secure_password
   ```

5. **Выполните миграции и сидеры**
   ```bash
   php artisan migrate --force
   php artisan db:seed
   ```

6. **Настройте хранилище**
   ```bash
   php artisan storage:link
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

7. **Настройте обработчик очередей** (добавьте в supervisor/systemd):
   ```bash
   php artisan queue:work --queue=image-processing --sleep=3 --tries=3
   ```

8. **Настройте cron** для плановых задач:
   ```bash
   * * * * * cd /path/to/ipp && php artisan schedule:run >> /dev/null 2>&1
   ```

### Способ 2: Web Installer

1. Загрузите файлы на веб-сервер
2. Убедитесь в правах на запись для `storage/`, `bootstrap/cache/`, `public/uploads/`
3. Перейдите по адресу `/install` в браузере
4. Следуйте пошаговому мастеру установки
5. Мастер выполнит:
   - Проверку системных требований
   - Тест соединения с БД
   - Создание начального администратора
   - Генерацию конфигурационных файлов
   - Выполнение миграций

## ⚙️ Конфигурация

### Переменные окружения

Ключевые настройки в `.env`:

```env
APP_NAME="Image Processing Platform"
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ipp
DB_USERNAME=your_user
DB_PASSWORD=your_password

# Queue (database, redis, sqs)
QUEUE_CONNECTION=database

# Cache (database, redis, file)
CACHE_STORE=redis

# File storage (local, s3)
FILESYSTEM_DISK=local

# IPP-specific settings
IPP_MAX_FILE_SIZE_MB=50
IPP_MAX_FILES_PER_JOB=100
IPP_MAX_FILES_FREE=10
IPP_STORAGE_TTL_HOURS=24
IPP_QUEUE=image-processing
IPP_QUEUE_TIMEOUT=300
```

### Конфигурация тарифов

Тарифы контролируют доступ пользователей к функциям. Структура по умолчанию:

| Функция | Free | Basic | Pro |
|---------|------|-------|-----|
| Макс. файлов/задание | 10 | 50 | 100 |
| Макс. размер файла | 10MB | 50MB | 100MB |
| Заданий в день | 5 | 50 | Unlimited |
| TTL хранения | 24ч | 72ч | 168ч |
| Приоритетная очередь | Нет | Да | Да |
| API доступ | Нет | Да | Да |
| Цена | $0 | $9/мес | $29/мес |

## 🔧 Архитектура

```
IPP/
├── app/
│   ├── Console/           # Artisan команды
│   ├── Http/              # Контроллеры и Middleware
│   │   ├── Controllers/Api/       # API эндпоинты
│   │   └── Controllers/Web/       # Web роуты
│   │       └── Admin/             # Панель администратора
│   ├── ImagePipeline/     # Движок обработки изображений
│   ├── Jobs/              # Задания очереди
│   ├── Models/            # Eloquent модели
│   ├── Services/          # Бизнес-логика
│   └── Providers/         # Сервис-провайдеры
├── config/ipp.php         # Конфигурация платформы
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── css/               # Tailwind стили
│   ├── js/                # Alpine.js и Vanilla JS
│   └── views/             # Blade шаблоны
├── routes/
│   ├── web.php
│   └── api.php
└── tests/
```

## 📊 Схема базы данных

Ключевые сущности:
- **users**: Зарегистрированные пользователи с назначенными тарифами
- **image_jobs**: Записи заданий обработки со статусом/пайплайном
- **image_job_files**: Записи отдельных файлов с метаданными
- **plans**: Тарифные планы с флагами функций
- **subscriptions**: Подписки на тарифы со статусом оплаты
- **locales**: Поддерживаемые языки
- **translation_entries**: Переводы UI
- **plan_translations**: Локализованные названия/описания тарифов

## 🔐 Безопасность

- CSRF-защита всех форм
- Rate limiting на API эндпоинтах
- Валидация и санитизация входных данных
- Защита от SQL-инъекций через Eloquent
- XSS-защита через авто-экранирование Blade
- Валидация загружаемых файлов (проверка MIME типов)
- Безопасное хеширование паролей (bcrypt)
- Опциональная двухфакторная аутентификация

## 🚀 Оптимизация производительности

### Конфигурация очередей
```php
// config/queue.php - Redis рекомендуется для production
'default' => 'redis',

'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
    'queue' => 'image-processing',
    'retry_after' => 3600,
],
```

### Стратегия кэширования
- Кэширование представлений в production
- Кэширование конфигурации: `php artisan config:cache`
- Кэширование роутов: `php artisan route:cache`
- Кэширование результатов запросов для локалей

### Обработка изображений
- Обработка в фоновой очереди
- Ограничение использования памяти на задание
- Автоматическая очистка временных файлов
- Опционально: внешний AI-сервис (RouterAI)

## 📝 API Документация

### Аутентификация
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

### Создание задания обработки
```http
POST /api/jobs
Authorization: Bearer {token}
Content-Type: multipart/form-data

files[]: [binary]
pipeline: [{"step":"resize","params":{"mode":"fit","width":1920}}]
output_format: webp
output_quality: 85
```

### Получение статуса задания
```http
GET /api/jobs/{uuid}
Authorization: Bearer {token}
```

## 🧪 Тестирование

```bash
# Запуск unit-тестов
php artisan test

# С покрытием
php artisan test --coverage

# Только feature-тесты
php artisan test --filter Feature
```

## 📈 Мониторинг

### Логи
- Приложение: `storage/logs/laravel.log`
- Очередь: `storage/logs/queue-worker.log`
- Очистка: `storage/logs/cleanup.log`

### Метрики
- Заданий очереди в час
- Среднее время обработки файла
- Частота ошибок по типам операций
- Тренды использования хранилища

### Оповещения администратора
Настройте email-уведомления для:
- Превышения порога ошибок
- Использования хранилища > 80%
- Очереди > 100 заданий

## 🤝 Участие в разработке

1. Форкните репозиторий
2. Создайте ветку функции: `git checkout -b feature/amazing-feature`
3. Зафиксируйте изменения: `git commit -m 'Add amazing feature'`
4. Отправьте в ветку: `git push origin feature/amazing-feature`
5. Откройте Pull Request

## 📜 Лицензия

MIT License - см. файл [LICENSE](LICENSE) для деталей.

## 🆘 Поддержка

- Документация: https://docs.yourdomain.com
- Issues: https://github.com/yourorg/ipp/issues
- Email: support@yourdomain.com

---

Создано с ❤️ на Laravel, Tailwind CSS и Alpine.js