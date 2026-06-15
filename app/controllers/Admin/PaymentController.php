<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\OrderModel;
use App\Models\UserModel;
use Services\Mailer;

class PaymentController extends Controller
{
    public function index(): void
    {
        $comprobantes = OrderModel::getPendingVouchers();
        $this->render('admin/pagos/index', [
            'pageTitle'    => 'Pagos por transferencia',
            'currentPage'  => 'pagos',
            'comprobantes' => $comprobantes,
        ], 'admin_layout');
    }

    public function approve(): void
    {
        $orderId = (int)($_POST['orden_id'] ?? 0);
        $orden   = OrderModel::findById($orderId);
        if (!$orden) { $this->flash('error', 'Orden no encontrada.'); $this->redirect(url('admin/pagos')); return; }

        OrderModel::updateStatus($orderId, 'pagada');
        $this->notifyClient($orden, 'pagada');

        $this->flash('success', 'Comprobante aprobado. Orden marcada como pagada.');
        $this->redirect(url('admin/pagos'));
    }

    public function reject(): void
    {
        $orderId = (int)($_POST['orden_id'] ?? 0);
        $orden   = OrderModel::findById($orderId);
        if (!$orden) { $this->flash('error', 'Orden no encontrada.'); $this->redirect(url('admin/pagos')); return; }

        OrderModel::updateStatus($orderId, 'cancelada');
        $this->notifyClient($orden, 'cancelada');

        $this->flash('success', 'Comprobante rechazado. Orden cancelada.');
        $this->redirect(url('admin/pagos'));
    }

    private function notifyClient(array $orden, string $estado): void
    {
        $user = UserModel::findById((int)$orden['usuario_id']);
        if (!$user) return;
        $mailer = new Mailer();
        $mailer->send(
            $user['correo'],
            'Actualización de tu orden #' . $orden['id'] . ' — VILUNA',
            'order_status',
            [
                'nombre'   => $user['nombre'],
                'ordenId'  => $orden['id'],
                'estado'   => OrderModel::ESTADOS[$estado],
                'ordenUrl' => url('mi-cuenta/ordenes/' . $orden['id']),
            ]
        );
    }
}
