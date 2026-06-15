<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\OrderModel;
use App\Models\UserModel;
use Services\Mailer;

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

        OrderModel::updateStatus((int)$id, $estado);

        // Notificar al cliente
        $user = UserModel::findById((int)$orden['usuario_id']);
        if ($user) {
            $mailer = new Mailer();
            $mailer->send(
                $user['correo'],
                'Actualización de tu orden #' . $id . ' — VILUNA',
                'order_status',
                [
                    'nombre'   => $user['nombre'],
                    'ordenId'  => $id,
                    'estado'   => OrderModel::ESTADOS[$estado],
                    'ordenUrl' => url('mi-cuenta/ordenes/' . $id),
                ]
            );
        }

        $this->flash('success', 'Estado actualizado a: ' . OrderModel::ESTADOS[$estado]);
        $this->redirect(url('admin/ordenes/' . $id));
    }
}
