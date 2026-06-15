<?php

namespace Core\Middleware;

/**
 * AdminMiddleware
 * Verifica que el usuario autenticado tenga rol 'admin'.
 * Si no, responde con HTTP 403.
 */
class AdminMiddleware
{
    public function handle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Primero verificar que esté autenticado
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Luego verificar que sea admin
        if (($_SESSION['user_rol'] ?? '') !== 'admin') {
            http_response_code(403);
            $errorClass = 'App\\Controllers\\ErrorController';
            if (class_exists($errorClass)) {
                (new $errorClass())->forbidden();
            } else {
                echo '<h1>403 — Acceso denegado</h1>';
            }
            exit;
        }
    }
}
