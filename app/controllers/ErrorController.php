<?php

namespace App\Controllers;

use Core\Controller;

/**
 * ErrorController — Maneja respuestas de error HTTP.
 */
class ErrorController extends Controller
{
    /** Página 404 — Recurso no encontrado */
    public function notFound(): void
    {
        http_response_code(404);
        $this->render('errors/404', ['pageTitle' => 'Página no encontrada']);
    }

    /** Página 403 — Acceso denegado */
    public function forbidden(): void
    {
        http_response_code(403);
        $this->render('errors/403', ['pageTitle' => 'Acceso denegado']);
    }

    /** Página 500 — Error interno */
    public function serverError(string $message = ''): void
    {
        http_response_code(500);
        $this->render('errors/500', [
            'pageTitle' => 'Error del servidor',
            'message'   => $message,
        ]);
    }
}
