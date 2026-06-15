<?php

namespace Core\Middleware;

/**
 * AuthMiddleware
 * Verifica que exista una sesión activa de usuario.
 * Si no, redirige a /login.
 */
class AuthMiddleware
{
    public function handle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            $currentUri = $_SERVER['REQUEST_URI'] ?? '';
            $redirect   = '/login';
            if ($currentUri && $currentUri !== '/login') {
                $redirect .= '?redirect=' . urlencode($currentUri);
            }
            header('Location: ' . $redirect);
            exit;
        }
    }
}
