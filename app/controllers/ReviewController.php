<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\ReviewModel;
use App\Models\ProductModel;

/**
 * ReviewController — Envío público de reseñas de productos.
 */
class ReviewController extends Controller
{
    /**
     * POST /producto/{slug}/resena
     * Crea una nueva reseña para el producto indicado.
     */
    public function store(string $slug): void
    {
        // 1. Verificar autenticación
        if (!$this->isAuthenticated()) {
            $this->redirect('/login');
            return;
        }

        $userId = $this->userId();

        // 2. Verificar que el producto exista
        $producto = ProductModel::findBySlug($slug);
        if (!$producto) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $productId = (int)$producto['id'];

        // 3. Verificar duplicado: usuario ya tiene reseña para este producto
        if (ReviewModel::userHasReview($userId, $productId)) {
            $this->flash('error', 'Ya has publicado una reseña para este producto.');
            $this->redirect('/producto/' . $slug);
            return;
        }

        // 4. Rate limit: máximo 3 reseñas en 24 horas
        if (ReviewModel::countRecentByUser($userId) >= 3) {
            $this->flash('error', 'Has alcanzado el límite de reseñas permitidas. Intenta de nuevo en 24 horas.');
            $this->redirect('/producto/' . $slug);
            return;
        }

        // 5. Validar datos de entrada
        $data = [
            'calificacion' => $_POST['calificacion'] ?? null,
            'comentario'   => $_POST['comentario'] ?? '',
        ];

        $errors = ReviewModel::validate($data);
        if (!empty($errors)) {
            $_SESSION['review_errors'] = $errors;
            $_SESSION['review_old'] = $data;
            $this->redirect('/producto/' . $slug);
            return;
        }

        // 6. Crear la reseña (sanitización se aplica dentro de ReviewModel::create)
        ReviewModel::create([
            'usuario_id'  => $userId,
            'producto_id' => $productId,
            'calificacion' => (int)$data['calificacion'],
            'comentario'   => $data['comentario'],
            'ip_address'   => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        // 7. Mensaje de confirmación
        $this->flash('success', 'Tu reseña ha sido enviada y está pendiente de aprobación.');
        $this->redirect('/producto/' . $slug);
    }
}
