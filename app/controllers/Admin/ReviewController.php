<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\ReviewModel;
use App\Models\ProductModel;

class ReviewController extends Controller
{
    /**
     * Lista de reseñas con filtros y paginación.
     */
    public function index(): void
    {
        $filters = [];
        if (!empty($_GET['producto_id'])) {
            $filters['producto_id'] = (int)$_GET['producto_id'];
        }
        if (!empty($_GET['usuario_id'])) {
            $filters['usuario_id'] = (int)$_GET['usuario_id'];
        }
        if (!empty($_GET['estado'])) {
            $filters['estado'] = $_GET['estado'];
        }
        if (!empty($_GET['calificacion'])) {
            $filters['calificacion'] = (int)$_GET['calificacion'];
        }
        if (!empty($_GET['fecha_desde'])) {
            $filters['fecha_desde'] = $_GET['fecha_desde'];
        }
        if (!empty($_GET['fecha_hasta'])) {
            $filters['fecha_hasta'] = $_GET['fecha_hasta'];
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $resenas = ReviewModel::findFiltered($filters, $limit, $offset);
        $total = ReviewModel::countFiltered($filters);
        $totalPages = max(1, (int)ceil($total / $limit));

        $productos = ProductModel::findAll();

        $this->render('admin/resenas/index', [
            'pageTitle'   => 'Reseñas',
            'currentPage' => 'resenas',
            'resenas'     => $resenas,
            'filters'     => $filters,
            'page'        => $page,
            'totalPages'  => $totalPages,
            'total'       => $total,
            'productos'   => $productos,
        ], 'admin_layout');
    }

    /**
     * Aprueba una reseña.
     */
    public function approve(string $id): void
    {
        $review = ReviewModel::findById((int)$id);
        if (!$review) {
            $this->flash('error', 'Reseña no encontrada.');
            $this->redirect(url('admin/resenas'));
            return;
        }

        ReviewModel::approve((int)$id);
        $this->flash('success', 'Reseña aprobada.');
        $this->redirect(url('admin/resenas'));
    }

    /**
     * Rechaza una reseña.
     */
    public function reject(string $id): void
    {
        $review = ReviewModel::findById((int)$id);
        if (!$review) {
            $this->flash('error', 'Reseña no encontrada.');
            $this->redirect(url('admin/resenas'));
            return;
        }

        ReviewModel::reject((int)$id);
        $this->flash('success', 'Reseña rechazada.');
        $this->redirect(url('admin/resenas'));
    }

    /**
     * Muestra formulario de edición de comentario.
     */
    public function edit(string $id): void
    {
        $review = ReviewModel::findById((int)$id);
        if (!$review) {
            $this->flash('error', 'Reseña no encontrada.');
            $this->redirect(url('admin/resenas'));
            return;
        }

        $this->render('admin/resenas/edit', [
            'pageTitle'   => 'Editar Reseña',
            'currentPage' => 'resenas',
            'resena'      => $review,
        ], 'admin_layout');
    }

    /**
     * Actualiza el texto del comentario.
     */
    public function update(string $id): void
    {
        $review = ReviewModel::findById((int)$id);
        if (!$review) {
            $this->flash('error', 'Reseña no encontrada.');
            $this->redirect(url('admin/resenas'));
            return;
        }

        $comentario = $_POST['comentario'] ?? '';
        $length = mb_strlen(trim($comentario));

        if ($length < 10 || $length > 1000) {
            $this->flash('error', 'El comentario debe tener entre 10 y 1000 caracteres.');
            $this->redirect(url('admin/resenas/' . $id . '/editar'));
            return;
        }

        ReviewModel::updateComment((int)$id, $comentario);
        $this->flash('success', 'Comentario actualizado.');
        $this->redirect(url('admin/resenas'));
    }

    /**
     * Elimina permanentemente una reseña.
     */
    public function delete(string $id): void
    {
        $review = ReviewModel::findById((int)$id);
        if (!$review) {
            $this->flash('error', 'Reseña no encontrada.');
            $this->redirect(url('admin/resenas'));
            return;
        }

        ReviewModel::delete((int)$id);
        $this->flash('success', 'Reseña eliminada.');
        $this->redirect(url('admin/resenas'));
    }
}
