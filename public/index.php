<?php

/**
 * VILUNA Jewelry Store — Front Controller
 * Punto de entrada único de la aplicación.
 */

declare(strict_types=1);

// ─── Autoload Composer ────────────────────────────────────────────────────────
$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    die('Vendor no encontrado. Ejecuta: composer install');
}
require $autoload;

// ─── Configuración ────────────────────────────────────────────────────────────
$configFile = dirname(__DIR__) . '/config/config.php';
if (!file_exists($configFile)) {
    die('Archivo de configuración no encontrado. Copia config/config.example.php a config/config.php');
}
require $configFile;

// ─── Helpers ─────────────────────────────────────────────────────────────────
require dirname(__DIR__) . '/core/helpers.php';

// ─── Middleware globales (seguridad + sesión) ─────────────────────────────────
(new \Core\Middleware\SecurityHeadersMiddleware())->handle();
(new \Core\Middleware\SessionMiddleware())->handle();

// ─── Router ───────────────────────────────────────────────────────────────────
$router = new \Core\Router();

// Cargar rutas
require dirname(__DIR__) . '/routes/web.php';

// Despachar petición
$router->dispatch();
