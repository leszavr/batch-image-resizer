# Image Processing Platform (IPP)

A powerful, web-based platform for batch image processing with support for resizing, conversion, cropping, rotation, mirroring, and AI-powered enhancements.

## 🚀 Features

### Core Image Processing
- **Batch Processing**: Process multiple images simultaneously
- **Format Conversion**: JPG, PNG, WebP, AVIF, GIF, TIFF
- **Resize**: Fit, Cover, Fixed dimensions, By width/height
- **Crop**: Coordinate-based cropping with position control
- **Rotate**: 90°, 180°, 270° rotation with automatic dimension handling
- **Flip**: Horizontal and vertical mirroring
- **Watermark**: Add text/image watermarks (plan-dependent)

### Advanced Features
- **Queue System**: Background processing with Laravel queues
- **Priority Processing**: Premium plans get dedicated high-priority queue
- **Multi-language Support**: Easy localization management via database
- **Plan-based Access**: Feature restrictions based on subscription tier
- **File Expiry**: Automatic cleanup of processed files after configurable TTL
- **API Access**: RESTful API for programmatic image processing

### Admin Panel
- **User Management**: Role-based access control (User, Admin, Superadmin)
- **Plan Management**: Flexible pricing tiers with customizable features
- **Job Monitoring**: Real-time status tracking and manual intervention
- **Analytics**: Usage statistics and revenue tracking
- **Localization**: Built-in translation management system

## 🛠 System Requirements

### Minimum
- PHP 8.2+
- MySQL 8.0+ or MariaDB 10.5+
- Composer 2.0+
- Node.js 18+ and npm
- 2GB RAM
- 10GB disk space

### Recommended
- PHP 8.3+
- MySQL 8.0+ or PostgreSQL
- Redis for queue management
- 4GB+ RAM
- 50GB+ SSD storage
- CDN for static assets

### PHP Extensions Required
- mysqli or pdo_mysql
- gd or imagick (imagick recommended)
- fileinfo
- mbstring
- openssl
- json
- curl
- zip

## 📦 Installation

### Method 1: Manual Installation

1. **Clone repository**
   ```bash
   git clone https://github.com/yourorg/ipp.git
   cd ipp
   ```

2. **Install dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   ```

3. **Create environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database** in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ipp
   DB_USERNAME=ipp_user
   DB_PASSWORD=your_secure_password
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate --force
   php artisan db:seed
   ```

6. **Configure storage**
   ```bash
   php artisan storage:link
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

7. **Setup queue worker** (add to supervisor/systemd):
   ```bash
   php artisan queue:work --queue=image-processing --sleep=3 --tries=3
   ```

8. **Configure cron** for scheduled tasks:
   ```bash
   * * * * * cd /path/to/ipp && php artisan schedule:run >> /dev/null 2>&1
   ```

### Method 2: Web Installer

1. Upload files to your web server
2. Ensure permissions on `storage/`, `bootstrap/cache/`, `public/uploads/`
3. Navigate to `/install` in your browser
4. Follow the step-by-step installation wizard
5. The installer will:
   - Check system requirements
   - Test database connection
   - Create initial admin user
   - Generate configuration files
   - Run migrations

## ⚙️ Configuration

### Environment Variables

Key settings in `.env`:

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

### Plan Configuration

Plans control user access to features. Default plan structure:

| Feature | Free | Basic | Pro |
|---------|------|-------|-----|
| Max files/job | 10 | 50 | 100 |
| Max file size | 10MB | 50MB | 100MB |
| Daily jobs | 5 | 50 | Unlimited |
| Storage TTL | 24h | 72h | 168h |
| Priority queue | No | Yes | Yes |
| API access | No | Yes | Yes |
| Price | $0 | $9/mo | $29/mo |

## 🔧 Architecture

```
IPP/
├── app/
│   ├── Console/           # Artisan commands
│   ├── Http/              # Controllers & Middleware
│   │   ├── Controllers/Api/       # API endpoints
│   │   └── Controllers/Web/       # Web routes
│   │       └── Admin/             # Admin panel
│   ├── ImagePipeline/     # Image processing engine
│   ├── Jobs/              # Queue jobs
│   ├── Models/            # Eloquent models
│   ├── Services/          # Business logic
│   └── Providers/         # Service providers
├── config/ipp.php         # Platform configuration
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── css/               # Tailwind styles
│   ├── js/                # Alpine.js & Vanilla JS
│   └── views/             # Blade templates
├── routes/
│   ├── web.php
│   └── api.php
└── tests/
```

## 📊 Database Schema

Key entities:
- **users**: Registered users with plan assignments
- **image_jobs**: Processing job records with status/pipeline
- **image_job_files**: Individual file records with metadata
- **plans**: Subscription tiers with feature flags
- **subscriptions**: Plan subscriptions with billing status
- **locales**: Supported languages
- **translation_entries**: UI translations
- **plan_translations**: Localized plan names/descriptions

## 🔐 Security

- CSRF protection on all forms
- Rate limiting on API endpoints
- Input validation and sanitization
- SQL injection prevention via Eloquent
- XSS protection via Blade's auto-escaping
- File upload validation (MIME type check)
- Secure password hashing (bcrypt)
- Optional 2FA support

## 🚀 Performance Optimization

### Queue Configuration
```php
// config/queue.php - Redis recommended for production
'default' => 'redis',

'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
    'queue' => 'image-processing',
    'retry_after' => 3600,
],
```

### Caching Strategy
- View caching in production
- Config caching: `php artisan config:cache`
- Route caching: `php artisan route:cache`
- Query result caching for locales

### Image Processing
- Process images in background queue
- Limit memory usage per job
- Automatic cleanup of temp files
- Optional: Use external AI service (RouterAI)

## 📝 API Documentation

### Authentication
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

### Create Processing Job
```http
POST /api/jobs
Authorization: Bearer {token}
Content-Type: multipart/form-data

files[]: [binary]
pipeline: [{"step":"resize","params":{"mode":"fit","width":1920}}]
output_format: webp
output_quality: 85
```

### Get Job Status
```http
GET /api/jobs/{uuid}
Authorization: Bearer {token}
```

## 🧪 Testing

```bash
# Run unit tests
php artisan test

# Run with coverage
php artisan test --coverage

# Feature tests only
php artisan test --filter Feature
```

## 📈 Monitoring

### Logs
- Application: `storage/logs/laravel.log`
- Queue: `storage/logs/queue-worker.log`
- Cleanup: `storage/logs/cleanup.log`

### Metrics
- Queue jobs processed per hour
- Average processing time per file
- Error rates by operation type
- Storage usage trends

### Admin Alerts
Configure email notifications for:
- Failed jobs exceeding threshold
- Storage usage > 80%
- Queue backlog > 100 jobs

## 🤝 Contributing

1. Fork the repository
2. Create feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open Pull Request

## 📜 License

MIT License - see [LICENSE](LICENSE) file for details.

## 🆘 Support

- Documentation: https://docs.yourdomain.com
- Issues: https://github.com/yourorg/ipp/issues
- Email: support@yourdomain.com

---

Built with ❤️ using Laravel, Tailwind CSS, and Alpine.js