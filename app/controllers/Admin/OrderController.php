<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\OrderModel;
use Services\NotificationService;

class OrderController extends Controller
{
    public function index(): void
    {
        $estado  = trim($_GET['estado'] ?? '');
        $ordenes = OrderModel::findAll($estado, 100);

        $this->render('admin/ordenes/index', [
            'pageTitle'   => 'Órdenes',
            'currentPage' => 'ordenes',
            'ordenes'     => $ordenes,
            'estadoFiltro'=> $estado,
            'estados'     => OrderModel::ESTADOS,
        ], 'admin_layout');
    }

    public function show(string $id): void
    {
        $orden = OrderModel::findById((int)$id);
        if (!$orden) { (new \App\Controllers\ErrorController())->notFound(); return; }

        $items = OrderModel::getDetails((int)$id);
        $this->render('admin/ordenes/show', [
            'pageTitle'   => 'Orden #' . $id,
            'currentPage' => 'ordenes',
            'orden'       => $orden,
            'items'       => $items,
            'estados'     => OrderModel::ESTADOS,
        ], 'admin_layout');
    }

    public function updateStatus(string $id): void
    {
        $orden  = OrderModel::findById((int)$id);
        if (!$orden) { (new \App\Controllers\ErrorController())->notFound(); return; }

        $estado = trim($_POST['estado'] ?? '');
        if (!array_key_exists($estado, OrderModel::ESTADOS)) {
            $this->flash('error', 'Estado inválido.');
            $this->redirect(url('admin/ordenes/' . $id));
            return;
        }

        $comentarios = trim($_POST['comentarios'] ?? '');

        OrderModel::updateStatus((int)$id, $estado);

        // Notificar al cliente del cambio de estado via NotificationService
        NotificationService::orderStatusChanged((int)$id, $estado, $comentarios ?: null);

        $this->flash('success', 'Estado actualizado a: ' . OrderModel::ESTADOS[$estado]);
        $this->redirect(url('admin/ordenes/' . $id));
    }
}
