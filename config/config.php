<?php
/**
 * VILUNA Jewelry Store — Configuración de desarrollo (Laragon)
 * IMPORTANTE: No versionar este archivo. Usar config.example.php como plantilla.
 */

// ─── Entorno ─────────────────────────────────────────────────────────────────
define('APP_ENV',  'development');
define('APP_URL',  'http://joyas.test');
define('APP_NAME', 'VILUNA');

// ─── Base de Datos ────────────────────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_PORT',    '3306');
define('DB_NAME',    'viluna');
define('DB_USER',    'viluna');
define('DB_PASS',    'MiC@rroI10');
define('DB_CHARSET', 'utf8mb4');

// ─── SMTP (PHPMailer) ─────────────────────────────────────────────────────────
define('MAIL_HOST',       'smtp.gmail.com');
define('MAIL_PORT',       587);
define('MAIL_USERNAME',   'tu-correo@gmail.com');
define('MAIL_PASSWORD',   'tu-app-password');
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_EMAIL', 'no-reply@viluna.com');
define('MAIL_FROM_NAME',  'VILUNA Jewelry');

// ─── Google OAuth ─────────────────────────────────────────────────────────────
define('GOOGLE_CLIENT_ID',     'TU_GOOGLE_CLIENT_ID.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'TU_GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI',  APP_URL . '/auth/google/callback');

// ─── Rutas del sistema ────────────────────────────────────────────────────────
define('ROOT_PATH',    dirname(__DIR__));
define('APP_PATH',     ROOT_PATH . '/app');
define('PUBLIC_PATH',  ROOT_PATH . '/public');
define('UPLOAD_PATH',  ROOT_PATH . '/uploads');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('LOG_PATH',     STORAGE_PATH . '/logs');

// ─── Configuración de errores (desarrollo) ───────────────────────────────────
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
