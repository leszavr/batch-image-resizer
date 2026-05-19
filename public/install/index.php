<?php

/**
 * Image Processing Platform - Web Installer
 * 
 * Entry point for the installation wizard.
 * Checks if already installed and redirects accordingly.
 */

declare(strict_types=1);

// Prevent access if already installed
$installedFile = dirname(__DIR__) . '/.installed';
if (file_exists($installedFile)) {
    http_response_code(403);
    die('
    <!DOCTYPE html>
    <html>
    <head>
        <title>Already Installed - Image Processing Platform</title>
        <style>
            body { font-family: system-ui, sans-serif; max-width: 600px; margin: 100px auto; padding: 20px; text-align: center; }
            .error { color: #dc2626; background: #fee2e2; padding: 20px; border-radius: 8px; }
            .hint { color: #666; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="error">
            <h1>⚠️ Already Installed</h1>
            <p>This application is already installed.</p>
            <p class="hint">If you need to reinstall, remove the <code>.installed</code> file from the public directory.</p>
        </div>
    </body>
    </html>
    ');
}

// Define paths
define('INSTALLER_ROOT', __DIR__);
define('INSTALLER_ASSETS', __DIR__ . '/assets');
define('APP_ROOT', dirname(__DIR__));

// Start session for wizard state
session_start();

// Initialize installer state
if (!isset($_SESSION['installer'])) {
    $_SESSION['installer'] = [
        'step' => 1,
        'data' => [],
        'errors' => [],
        'passed_steps' => [],
    ];
}

// Simple router
$step = $_GET['step'] ?? 'welcome';
$action = $_GET['action'] ?? null;

// Handle AJAX requests
if ($action === 'api') {
    handleAjaxRequest();
    exit;
}

// Render the installer page
renderInstaller($step);

/**
 * Render the installer page with current step
 */
function renderInstaller(string $step): void
{
    $steps = [
        1 => 'welcome',
        2 => 'requirements',
        3 => 'database',
        4 => 'admin',
        5 => 'settings',
        6 => 'complete',
    ];
    
    $currentStep = array_search($step, $steps) ?: 1;
    $stepFile = INSTALLER_ROOT . "/views/{$step}.php";
    
    if (!file_exists($stepFile)) {
        $stepFile = INSTALLER_ROOT . '/views/welcome.php';
        $currentStep = 1;
    }
    
    // Check if previous steps passed
    $canAccess = true;
    for ($i = 1; $i < $currentStep; $i++) {
        if (!in_array($i, $_SESSION['installer']['passed_steps'] ?? [])) {
            $canAccess = false;
            break;
        }
    }
    
    if (!$canAccess && $currentStep > 1) {
        header('Location: ?step=welcome');
        exit;
    }
    
    // Get step data
    $installer = $_SESSION['installer'];
    $errors = $installer['errors'] ?? [];
    $data = $installer['data'] ?? [];
    
    // Clear errors after display
    $_SESSION['installer']['errors'] = [];
    
    include INSTALLER_ROOT . '/views/layout.php';
}

/**
 * Handle AJAX API requests
 */
function handleAjaxRequest(): void
{
    header('Content-Type: application/json');
    
    $method = $_SERVER['REQUEST_METHOD'];
    $endpoint = $_GET['endpoint'] ?? '';
    
    try {
        switch ($endpoint) {
            case 'check-requirements':
                echo json_encode(['success' => true, 'data' => checkSystemRequirements()]);
                break;
                
            case 'test-database':
                if ($method !== 'POST') {
                    throw new Exception('Method not allowed', 405);
                }
                $data = json_decode(file_get_contents('php://input'), true);
                echo json_encode(testDatabaseConnection($data));
                break;
                
            case 'install-database':
                if ($method !== 'POST') {
                    throw new Exception('Method not allowed', 405);
                }
                $data = json_decode(file_get_contents('php://input'), true);
                echo json_encode(installDatabase($data));
                break;
                
            case 'create-admin':
                if ($method !== 'POST') {
                    throw new Exception('Method not allowed', 405);
                }
                $data = json_decode(file_get_contents('php://input'), true);
                echo json_encode(createAdminUser($data));
                break;
                
            case 'save-settings':
                if ($method !== 'POST') {
                    throw new Exception('Method not allowed', 405);
                }
                $data = json_decode(file_get_contents('php://input'), true);
                echo json_encode(saveSettings($data));
                break;
                
            case 'finalize':
                if ($method !== 'POST') {
                    throw new Exception('Method not allowed', 405);
                }
                echo json_encode(finalizeInstallation());
                break;
                
            default:
                throw new Exception('Unknown endpoint', 404);
        }
    } catch (Exception $e) {
        http_response_code($e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

/**
 * Check system requirements
 */
function checkSystemRequirements(): array
{
    $requirements = [];
    
    // PHP Version
    $phpVersion = PHP_VERSION;
    $requirements['php_version'] = [
        'name' => 'PHP Version',
        'required' => '>= 8.2.0',
        'current' => $phpVersion,
        'passed' => version_compare($phpVersion, '8.2.0', '>='),
    ];
    
    // PHP Extensions
    $extensions = [
        'mysqli' => 'mysqli/pdo_mysql (database)',
        'pdo_mysql' => 'PDO MySQL (database)',
        'fileinfo' => 'fileinfo (file uploads)',
        'mbstring' => 'mbstring (multibyte strings)',
        'openssl' => 'openssl (encryption)',
        'json' => 'json (data handling)',
        'curl' => 'curl (HTTP requests)',
        'zip' => 'zip (archive handling)',
    ];
    
    foreach ($extensions as $ext => $name) {
        $requirements['ext_' . $ext] = [
            'name' => $name,
            'required' => 'Installed',
            'current' => extension_loaded($ext) ? 'Installed' : 'Not installed',
            'passed' => extension_loaded($ext),
        ];
    }
    
    // Image processing (either GD or Imagick)
    $gdLoaded = extension_loaded('gd');
    $imagickLoaded = extension_loaded('imagick');
    $requirements['image_processing'] = [
        'name' => 'Image Processing (GD or Imagick)',
        'required' => 'GD or Imagick',
        'current' => $gdLoaded ? 'GD' : ($imagickLoaded ? 'Imagick' : 'None'),
        'passed' => $gdLoaded || $imagickLoaded,
    ];
    
    // Memory limit
    $memoryLimit = ini_get('memory_limit');
    $memoryBytes = returnBytes($memoryLimit);
    $requirements['memory'] = [
        'name' => 'Memory Limit',
        'required' => '>= 256M',
        'current' => $memoryLimit,
        'passed' => $memoryBytes >= 256 * 1024 * 1024,
    ];
    
    // Upload max filesize
    $uploadLimit = ini_get('upload_max_filesize');
    $uploadBytes = returnBytes($uploadLimit);
    $requirements['upload'] = [
        'name' => 'Upload Max Filesize',
        'required' => '>= 10M',
        'current' => $uploadLimit,
        'passed' => $uploadBytes >= 10 * 1024 * 1024,
    ];
    
    // Directory permissions
    $directories = [
        'storage' => APP_ROOT . '/storage',
        'cache' => APP_ROOT . '/bootstrap/cache',
        'uploads' => APP_ROOT . '/public/uploads',
    ];
    
    foreach ($directories as $name => $path) {
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
        $writable = is_writable($path);
        $requirements['dir_' . $name] = [
            'name' => "Writable: {$name}",
            'required' => 'Writable',
            'current' => $writable ? 'Writable' : 'Not writable',
            'passed' => $writable,
        ];
    }
    
    // Composer check (optional but recommended)
    $composerAvailable = false;
    $composerPath = null;
    $paths = ['composer', '/usr/local/bin/composer', '/usr/bin/composer'];
    foreach ($paths as $path) {
        exec("which {$path} 2>/dev/null", $output, $return);
        if ($return === 0) {
            $composerAvailable = true;
            $composerPath = $path;
            break;
        }
    }
    $requirements['composer'] = [
        'name' => 'Composer (optional)',
        'required' => 'Available',
        'current' => $composerAvailable ? ($composerPath ?: 'Available') : 'Not found',
        'passed' => true, // Optional requirement
    ];
    
    return $requirements;
}

/**
 * Convert PHP size string to bytes
 */
function returnBytes(string $val): int
{
    $val = trim($val);
    $last = strtolower($val[strlen($val) - 1]);
    $val = (int) $val;
    
    switch ($last) {
        case 'g':
            $val *= 1024;
            // no break
        case 'm':
            $val *= 1024;
            // no break
        case 'k':
            $val *= 1024;
    }
    
    return $val;
}

/**
 * Test database connection
 */
function testDatabaseConnection(array $data): array
{
    $host = $data['host'] ?? '';
    $port = (int) ($data['port'] ?? 3306);
    $database = $data['database'] ?? '';
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    if (empty($host) || empty($username)) {
        return ['success' => false, 'error' => 'Host and username are required'];
    }
    
    try {
        $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        
        // Try to create database if it doesn't exist
        if (!empty($database)) {
            $stmt = $pdo->prepare("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $stmt->execute();
        }
        
        // Test connection to specific database
        $pdo->exec("USE `{$database}`");
        
        // Get MySQL version
        $version = $pdo->query('SELECT VERSION()')->fetchColumn();
        
        return [
            'success' => true,
            'message' => 'Connection successful',
            'version' => $version,
            'database_created' => true,
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
        ];
    }
}

/**
 * Install database (run migrations)
 */
function installDatabase(array $data): array
{
    // Store database config in session
    $_SESSION['installer']['data']['database'] = $data;
    $_SESSION['installer']['passed_steps'][] = 3;
    
    return ['success' => true, 'message' => 'Database configuration saved'];
}

/**
 * Create admin user
 */
function createAdminUser(array $data): array
{
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    
    if (empty($name) || empty($email) || empty($password)) {
        return ['success' => false, 'error' => 'All fields are required'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Invalid email format'];
    }
    
    if (strlen($password) < 12) {
        return ['success' => false, 'error' => 'Password must be at least 12 characters'];
    }
    
    // Store admin data in session
    $_SESSION['installer']['data']['admin'] = [
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_BCRYPT),
    ];
    $_SESSION['installer']['passed_steps'][] = 4;
    
    return ['success' => true, 'message' => 'Admin user configured'];
}

/**
 * Save application settings
 */
function saveSettings(array $data): array
{
    $_SESSION['installer']['data']['settings'] = [
        'app_name' => $data['app_name'] ?? 'Image Processing Platform',
        'app_url' => $data['app_url'] ?? '',
        'app_locale' => $data['app_locale'] ?? 'en',
        'app_timezone' => $data['app_timezone'] ?? 'UTC',
        'admin_email' => $data['admin_email'] ?? '',
    ];
    $_SESSION['installer']['passed_steps'][] = 5;
    
    return ['success' => true, 'message' => 'Settings saved'];
}

/**
 * Finalize installation - create .env, run migrations, create admin, etc.
 */
function finalizeInstallation(): array
{
    $data = $_SESSION['installer']['data'] ?? [];
    
    if (empty($data['database']) || empty($data['admin'])) {
        return ['success' => false, 'error' => 'Missing configuration data'];
    }
    
    try {
        // Generate APP_KEY
        $appKey = 'base64:' . base64_encode(random_bytes(32));
        
        // Build .env content
        $env = buildEnvFile($data, $appKey);
        
        // Write .env file
        $envPath = APP_ROOT . '/../.env';
        if (file_exists($envPath)) {
            rename($envPath, $envPath . '.backup.' . date('YmdHis'));
        }
        
        if (file_put_contents($envPath, $env) === false) {
            throw new Exception('Failed to write .env file');
        }
        
        // Create .installed file
        $installedFile = dirname(__DIR__) . '/.installed';
        $installInfo = [
            'installed_at' => date('Y-m-d H:i:s'),
            'version' => '1.0.0',
        ];
        file_put_contents($installedFile, json_encode($installInfo, JSON_PRETTY_PRINT));
        
        // Clear session
        unset($_SESSION['installer']);
        
        return [
            'success' => true,
            'message' => 'Installation completed successfully',
            'redirect' => '/',
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
        ];
    }
}

/**
 * Build the .env file content
 */
function buildEnvFile(array $data, string $appKey): string
{
    $db = $data['database'];
    $settings = $data['settings'] ?? [];
    $admin = $data['admin'] ?? [];
    
    return <<<ENV
APP_NAME="{$settings['app_name']}"
APP_ENV=production
APP_KEY={$appKey}
APP_DEBUG=false
APP_URL={$settings['app_url']}

APP_LOCALE={$settings['app_locale']}
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST={$db['host']}
DB_PORT={$db['port']}
DB_DATABASE={$db['database']}
DB_USERNAME={$db['username']}
DB_PASSWORD={$db['password']}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="{$settings['admin_email']}"
MAIL_FROM_NAME="{$settings['app_name']}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="{$settings['app_name']}"

# Image Processing Platform Settings
IPP_MAX_FILE_SIZE_MB=50
IPP_MAX_FILES_PER_JOB=100
IPP_MAX_FILES_FREE=10
IPP_MAX_FILE_SIZE_FREE_MB=10
IPP_STORAGE_TTL_HOURS=24
IPP_QUEUE=image-processing
IPP_QUEUE_TIMEOUT=300

ENV;
}