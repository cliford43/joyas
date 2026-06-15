<?php

namespace Core\Middleware;

/**
 * CsrfMiddleware
 * - En GET: genera un token CSRF en sesión si no existe.
 * - En POST: valida que el token enviado coincida con el de sesión.
 *   Si no coincide, aborta con HTTP 419.
 */
class CsrfMiddleware
{
    public function handle(): void
    {
        // Asegurar que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        if ($method === 'GET') {
            $this->generateToken();
            return;
        }

        if ($method === 'POST') {
            $this->validateToken();
        }
    }

    /**
     * Genera el token CSRF y lo almacena en sesión.
     */
    public function generateToken(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    /**
     * Devuelve el token actual de sesión (o lo genera si no existe).
     */
    public static function getToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    /**
     * Valida el token CSRF en peticiones POST.
     * Aborta con 419 si el token es inválido.
     */
    private function validateToken(): void
    {
        $tokenPost    = $_POST['_csrf_token'] ?? '';
        $tokenSession = $_SESSION['_csrf_token'] ?? '';

        if (empty($tokenPost) || empty($tokenSession) || !hash_equals($tokenSession, $tokenPost)) {
            http_response_code(419);
            $_SESSION['flash_error'] = 'Sesión expirada. Por favor, intenta de nuevo.';

            $referer = $_SERVER['HTTP_REFERER'] ?? '/';
            header('Location: ' . $referer);
            exit;
        }
    }
}
