<?php

namespace Core;

/**
 * Router — Enrutador MVC de VILUNA
 * Soporta parámetros dinámicos {param}, middleware por ruta y fallback 404.
 */
class Router
{
    /** @var array<int, array{method: string, pattern: string, handler: string, middleware: string[]}> */
    private array $routes = [];

    private string $basePath = '';

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /** Registra ruta GET */
    public function get(string $uri, string $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $uri, $handler, $middleware);
    }

    /** Registra ruta POST */
    public function post(string $uri, string $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $uri, $handler, $middleware);
    }

    private function addRoute(string $method, string $uri, string $handler, array $middleware): void
    {
        $this->routes[] = [
            'method'     => strtoupper($method),
            'pattern'    => $uri,
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    /**
     * Despacha la petición actual al controlador correspondiente.
     */
    public function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri    = $this->getCurrentUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchRoute($route['pattern'], $uri);
            if ($params === false) {
                continue;
            }

            // Ejecutar middleware
            $this->runMiddleware($route['middleware']);

            // Despachar controlador
            $this->callHandler($route['handler'], $params);
            return;
        }

        // Fallback 404
        $this->notFound();
    }

    /**
     * Devuelve la URI actual limpiada (sin query string, sin base path).
     */
    public function getCurrentUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Quitar query string
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        // Quitar basePath si aplica
        if ($this->basePath !== '' && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }

        $uri = rawurldecode($uri);
        $uri = '/' . trim($uri, '/');

        return $uri === '' ? '/' : $uri;
    }

    /**
     * Compara el patrón de ruta con la URI y extrae parámetros.
     * Retorna array de parámetros o false si no coincide.
     */
    private function matchRoute(string $pattern, string $uri): array|false
    {
        // Convertir {param} en grupo de captura nombrado
        $regex = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#u';

        if (!preg_match($regex, $uri, $matches)) {
            return false;
        }

        // Filtrar solo grupos nombrados
        $params = array_filter(
            $matches,
            fn($key) => is_string($key),
            ARRAY_FILTER_USE_KEY
        );

        return $params;
    }

    /**
     * Ejecuta los middleware en el orden registrado.
     */
    private function runMiddleware(array $middlewareList): void
    {
        $map = [
            'security' => \Core\Middleware\SecurityHeadersMiddleware::class,
            'session'  => \Core\Middleware\SessionMiddleware::class,
            'csrf'     => \Core\Middleware\CsrfMiddleware::class,
            'auth'     => \Core\Middleware\AuthMiddleware::class,
            'admin'    => \Core\Middleware\AdminMiddleware::class,
        ];

        foreach ($middlewareList as $name) {
            $class = $map[$name] ?? null;
            if ($class && class_exists($class)) {
                (new $class())->handle();
            }
        }
    }

    /**
     * Invoca el controlador/acción con los parámetros extraídos.
     * El handler tiene el formato "ControllerClass@method" o "Admin\ControllerClass@method".
     */
    private function callHandler(string $handler, array $params): void
    {
        [$controllerName, $action] = explode('@', $handler, 2);

        // Buscar en App\Controllers o App\Controllers\Admin
        $fullClass = 'App\\Controllers\\' . $controllerName;
        if (!class_exists($fullClass)) {
            $fullClass = 'App\\Controllers\\Admin\\' . ltrim($controllerName, 'Admin\\');
        }

        if (!class_exists($fullClass)) {
            $this->notFound();
            return;
        }

        $controller = new $fullClass();

        if (!method_exists($controller, $action)) {
            $this->notFound();
            return;
        }

        // Pasar parámetros como argumentos al método
        call_user_func_array([$controller, $action], $params);
    }

    /**
     * Respuesta 404 — usa ErrorController si está disponible.
     */
    private function notFound(): void
    {
        http_response_code(404);

        $errorClass = 'App\\Controllers\\ErrorController';
        if (class_exists($errorClass)) {
            (new $errorClass())->notFound();
        } else {
            echo '<h1>404 — Página no encontrada</h1>';
        }
    }
}
