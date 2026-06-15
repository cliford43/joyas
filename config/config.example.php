<?php
/**
 * VILUNA Jewelry Store — Plantilla de Configuración
 * Copiar a config/config.php y ajustar los valores reales.
 * ¡NO versionar config.php!
 */

// ─── Entorno ─────────────────────────────────────────────────────────────────
define('APP_ENV',  'development');   // development | production
define('APP_URL',  'http://viluna.test'); // URL base sin barra final
define('APP_NAME', 'VILUNA');

// ─── Base de Datos ────────────────────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_PORT',    '3306');
define('DB_NAME',    'viluna');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

// ─── SMTP (PHPMailer) ─────────────────────────────────────────────────────────
define('MAIL_HOST',       'smtp.example.com');
define('MAIL_PORT',       587);
define('MAIL_USERNAME',   'no-reply@viluna.com');
define('MAIL_PASSWORD',   'your_smtp_password');
define('MAIL_ENCRYPTION', 'tls');          // tls | ssl
define('MAIL_FROM_EMAIL', 'no-reply@viluna.com');
define('MAIL_FROM_NAME',  'VILUNA Jewelry');

// ─── Google OAuth ─────────────────────────────────────────────────────────────
define('GOOGLE_CLIENT_ID',     'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI',  APP_URL . '/auth/google/callback');

// ─── Rutas del sistema ────────────────────────────────────────────────────────
define('ROOT_PATH',    dirname(__DIR__));
define('APP_PATH',     ROOT_PATH . '/app');
define('PUBLIC_PATH',  ROOT_PATH . '/public');
define('UPLOAD_PATH',  ROOT_PATH . '/uploads');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('LOG_PATH',     STORAGE_PATH . '/logs');

// ─── Configuración de errores ─────────────────────────────────────────────────
if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', LOG_PATH . '/error.log');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}
