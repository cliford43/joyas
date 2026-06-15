<?php

namespace Core\Middleware;

/**
 * SessionMiddleware
 * Inicia la sesión PHP si no ha sido iniciada.
 */
class SessionMiddleware
{
    public function handle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configuración segura de cookies de sesión
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }
}
