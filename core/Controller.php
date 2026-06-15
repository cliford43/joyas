<?php

namespace Core;

use Core\Middleware\CsrfMiddleware;

/**
 * Core\Controller — Clase base para todos los controladores
 * Provee método render() con escape de salida y utilidades de respuesta.
 */
abstract class Controller
{
    /**
     * Renderiza una vista pasando variables saneadas.
     *
     * @param string $view   Ruta relativa a app/views/ sin extensión, ej: 'home/index'
     * @param array  $data   Variables a exponer en la vista (se escapan automáticamente si son string)
     * @param string $layout Layout a usar ('layout', 'admin_layout', o '' para sin layout)
     */
    protected function render(string $view, array $data = [], string $layout = 'layout'): void
    {
        // Escapar todos los valores string del array $data
        $escaped = $this->escapeData($data);

        // Extraer variables para la vista
        extract($escaped, EXTR_SKIP);

        // Generar token CSRF disponible en vistas
        $csrfToken = CsrfMiddleware::getToken();

        // Capturar el contenido de la vista
        $viewPath = APP_PATH . '/views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "Vista no encontrada: {$view}";
            return;
        }

        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        if ($layout === '') {
            echo $content;
            return;
        }

        // Renderizar con layout
        $layoutPath = APP_PATH . '/views/layouts/' . $layout . '.php';
        if (!file_exists($layoutPath)) {
            echo $content;
            return;
        }

        include $layoutPath;
    }

    /**
     * Aplica htmlspecialchars recursivamente a strings del array.
     * Los valores que NO son string (arrays, objetos, null, int, float, bool) se pasan sin modificar.
     */
    protected function escapeData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            } elseif (is_array($value)) {
                $data[$key] = $this->escapeData($value);
            }
            // int, float, bool, null, object → sin modificar
        }
        return $data;
    }

    /**
     * Responde con JSON (para endpoints AJAX).
     */
    protected function json(mixed $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Redirige a una URL.
     */
    protected function redirect(string $url, int $code = 302): void
    {
        header('Location: ' . $url, true, $code);
        exit;
    }

    /**
     * Detecta si la petición es AJAX.
     */
    protected function isAjax(): bool
    {
        return (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');
    }

    /**
     * Almacena un mensaje flash en sesión.
     */
    protected function flash(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_' . $type] = $message;
    }

    /**
     * Obtiene y elimina el mensaje flash de sesión.
     */
    protected function getFlash(string $type): ?string
    {
        $key = 'flash_' . $type;
        $msg = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $msg;
    }

    /**
     * Verifica si el usuario está autenticado.
     */
    protected function isAuthenticated(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    /**
     * Obtiene el ID del usuario autenticado.
     */
    protected function userId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    /**
     * Obtiene el rol del usuario autenticado.
     */
    protected function userRole(): ?string
    {
        return $_SESSION['user_rol'] ?? null;
    }
}
