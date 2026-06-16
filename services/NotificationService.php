<?php

namespace Services;

use App\Models\EmailTemplateModel;
use App\Models\OrderModel;
use App\Models\ReviewModel;
use App\Models\ProductModel;
use App\Models\ConfigModel;
use Services\Mailer;

/**
 * NotificationService — Servicio centralizado de notificaciones por correo.
 * Cada método carga la plantilla desde DB vía EmailTemplateModel::render().
 * Si la plantilla no existe en DB, usa la plantilla PHP existente como fallback.
 * Nunca interrumpe el flujo principal si el envío falla.
 */
class NotificationService
{
    /**
     * Envía correo de bienvenida al usuario recién registrado.
     *
     * @param array $user {nombre, apellido, correo}
     */
    public static function welcomeEmail(array $user): void
    {
        $variables = [
            'nombre_cliente' => $user['nombre'] ?? '',
            'apellido'       => $user['apellido'] ?? '',
            'correo'         => $user['correo'] ?? '',
            'login_url'      => url('login'),
            'tienda_nombre'  => defined('APP_NAME') ? APP_NAME : 'VILUNA',
            'tienda_url'     => defined('APP_URL') ? APP_URL : '',
        ];

        $rendered = EmailTemplateModel::render('welcome', $variables);

        $mailer = new Mailer();

        if ($rendered) {
            $mailer->send($user['correo'], $rendered['subject'], 'welcome', [
                'htmlOverride' => $rendered['body'],
            ]);
        } else {
            // Fallback to PHP template
            $mailer->send($user['correo'], 'Bienvenido a VILUNA', 'welcome', $variables);
        }
    }

    /**
     * Envía correo de confirmación de pedido al cliente.
     *
     * @param int $orderId
     */
    public static function orderConfirmation(int $orderId): void
    {
        $order = OrderModel::findById($orderId);
        if (!$order) {
            return;
        }

        $items = OrderModel::getDetails($orderId);

        $metodoPago = match ($order['metodo_pago'] ?? '') {
            'contra_entrega' => 'Contra entrega',
            'transferencia'  => 'Transferencia bancaria',
            default          => $order['metodo_pago'] ?? '',
        };

        $variables = [
            'nombre_cliente'    => $order['nombre'] ?? '',
            'numero_pedido'     => $orderId,
            'fecha_pedido'      => $order['fecha_creacion'] ?? date('Y-m-d H:i:s'),
            'subtotal'          => number_format((float)($order['subtotal'] ?? 0), 2),
            'descuento'         => number_format((float)($order['descuento_cupon'] ?? 0), 2),
            'total'             => number_format((float)($order['total'] ?? 0), 2),
            'metodo_pago'       => $metodoPago,
            'direccion_entrega' => $order['direccion_entrega'] ?? '',
            'tienda_nombre'     => defined('APP_NAME') ? APP_NAME : 'VILUNA',
        ];

        $rendered = EmailTemplateModel::render('order_confirmation', $variables);

        $mailer = new Mailer();

        if ($rendered) {
            $mailer->send($order['correo'], $rendered['subject'], 'order_confirmation', [
                'htmlOverride' => $rendered['body'],
                'nombre'       => $order['nombre'],
                'ordenId'      => $orderId,
                'items'        => $items,
                'total'        => (float)($order['total'] ?? 0),
                'metodoPago'   => $metodoPago,
                'direccion'    => $order['direccion_entrega'] ?? '',
            ]);
        } else {
            // Fallback to existing PHP template
            $mailer->send(
                $order['correo'],
                'Orden confirmada #' . $orderId . ' — VILUNA',
                'order_confirmation',
                [
                    'nombre'    => $order['nombre'],
                    'ordenId'   => $orderId,
                    'items'     => $items,
                    'total'     => (float)($order['total'] ?? 0),
                    'metodoPago'=> $metodoPago,
                    'direccion' => $order['direccion_entrega'] ?? '',
                ]
            );
        }
    }

    /**
     * Envía correo de confirmación de pago al cliente.
     *
     * @param int $orderId
     */
    public static function paymentConfirmed(int $orderId): void
    {
        $order = OrderModel::findById($orderId);
        if (!$order) {
            return;
        }

        $variables = [
            'nombre_cliente'  => $order['nombre'] ?? '',
            'numero_pedido'   => $orderId,
            'monto_pagado'    => number_format((float)($order['total'] ?? 0), 2),
            'fecha_confirmacion' => date('Y-m-d H:i:s'),
            'estado_pedido'   => 'Pago confirmado',
            'orden_url'       => url('mi-cuenta/ordenes/' . $orderId),
            'tienda_nombre'   => defined('APP_NAME') ? APP_NAME : 'VILUNA',
        ];

        $rendered = EmailTemplateModel::render('payment_confirmed', $variables);

        $mailer = new Mailer();

        if ($rendered) {
            $mailer->send($order['correo'], $rendered['subject'], 'payment_confirmed', [
                'htmlOverride' => $rendered['body'],
            ]);
        } else {
            // Fallback to PHP template
            $mailer->send(
                $order['correo'],
                'Pago confirmado — Orden #' . $orderId . ' — VILUNA',
                'payment_confirmed',
                $variables
            );
        }
    }

    /**
     * Envía correo de cambio de estado de pedido al cliente.
     *
     * @param int         $orderId
     * @param string      $newStatus
     * @param string|null $comments
     */
    public static function orderStatusChanged(int $orderId, string $newStatus, ?string $comments = null): void
    {
        $order = OrderModel::findById($orderId);
        if (!$order) {
            return;
        }

        $statusLabels = OrderModel::ESTADOS;
        $statusLabel  = $statusLabels[$newStatus] ?? $newStatus;

        $variables = [
            'nombre_cliente'  => $order['nombre'] ?? '',
            'numero_pedido'   => $orderId,
            'nuevo_estado'    => $statusLabel,
            'fecha_actualizacion' => date('Y-m-d H:i:s'),
            'comentarios'     => $comments ?? '',
            'orden_url'       => url('mi-cuenta/ordenes/' . $orderId),
            'tienda_nombre'   => defined('APP_NAME') ? APP_NAME : 'VILUNA',
        ];

        $rendered = EmailTemplateModel::render('order_status', $variables);

        $mailer = new Mailer();

        if ($rendered) {
            $mailer->send($order['correo'], $rendered['subject'], 'order_status', [
                'htmlOverride' => $rendered['body'],
            ]);
        } else {
            // Fallback to PHP template
            $mailer->send(
                $order['correo'],
                'Tu pedido #' . $orderId . ' cambió a: ' . $statusLabel . ' — VILUNA',
                'order_status',
                $variables
            );
        }
    }

    /**
     * Notifica al admin sobre un nuevo usuario registrado.
     *
     * @param array $user {nombre, apellido, correo}
     */
    public static function adminNewUser(array $user): void
    {
        $adminEmail = self::getAdminEmail();
        if (!$adminEmail) {
            return;
        }

        $variables = [
            'nombre_cliente' => ($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? ''),
            'correo_cliente' => $user['correo'] ?? '',
            'fecha'          => date('Y-m-d H:i:s'),
            'tienda_nombre'  => defined('APP_NAME') ? APP_NAME : 'VILUNA',
        ];

        $rendered = EmailTemplateModel::render('admin_new_user', $variables);

        $mailer = new Mailer();

        if ($rendered) {
            $mailer->send($adminEmail, $rendered['subject'], 'admin_notification', [
                'htmlOverride' => $rendered['body'],
            ]);
        } else {
            $mailer->send(
                $adminEmail,
                'Nuevo usuario registrado — VILUNA',
                'admin_notification',
                array_merge($variables, [
                    'titulo' => 'Nuevo usuario registrado',
                    'mensaje' => "Se ha registrado un nuevo usuario: {$variables['nombre_cliente']} ({$variables['correo_cliente']})",
                ])
            );
        }
    }

    /**
     * Notifica al admin sobre un nuevo pedido.
     *
     * @param int $orderId
     */
    public static function adminNewOrder(int $orderId): void
    {
        $adminEmail = self::getAdminEmail();
        if (!$adminEmail) {
            return;
        }

        $order = OrderModel::findById($orderId);
        if (!$order) {
            return;
        }

        $variables = [
            'numero_pedido'   => $orderId,
            'nombre_cliente'  => ($order['nombre'] ?? '') . ' ' . ($order['apellido'] ?? ''),
            'correo_cliente'  => $order['correo'] ?? '',
            'total'           => number_format((float)($order['total'] ?? 0), 2),
            'metodo_pago'     => $order['metodo_pago'] ?? '',
            'fecha'           => $order['fecha_creacion'] ?? date('Y-m-d H:i:s'),
            'tienda_nombre'   => defined('APP_NAME') ? APP_NAME : 'VILUNA',
        ];

        $rendered = EmailTemplateModel::render('admin_new_order', $variables);

        $mailer = new Mailer();

        if ($rendered) {
            $mailer->send($adminEmail, $rendered['subject'], 'admin_notification', [
                'htmlOverride' => $rendered['body'],
            ]);
        } else {
            $mailer->send(
                $adminEmail,
                'Nuevo pedido #' . $orderId . ' — VILUNA',
                'admin_notification',
                array_merge($variables, [
                    'titulo' => 'Nuevo pedido recibido',
                    'mensaje' => "Pedido #{$orderId} de {$variables['nombre_cliente']} por \${$variables['total']}",
                ])
            );
        }
    }

    /**
     * Notifica al admin sobre un comprobante de pago recibido.
     *
     * @param int $orderId
     */
    public static function adminPaymentReceived(int $orderId): void
    {
        $adminEmail = self::getAdminEmail();
        if (!$adminEmail) {
            return;
        }

        $order = OrderModel::findById($orderId);
        if (!$order) {
            return;
        }

        $variables = [
            'numero_pedido'   => $orderId,
            'nombre_cliente'  => ($order['nombre'] ?? '') . ' ' . ($order['apellido'] ?? ''),
            'correo_cliente'  => $order['correo'] ?? '',
            'total'           => number_format((float)($order['total'] ?? 0), 2),
            'fecha'           => date('Y-m-d H:i:s'),
            'tienda_nombre'   => defined('APP_NAME') ? APP_NAME : 'VILUNA',
        ];

        $rendered = EmailTemplateModel::render('admin_payment_received', $variables);

        $mailer = new Mailer();

        if ($rendered) {
            $mailer->send($adminEmail, $rendered['subject'], 'admin_notification', [
                'htmlOverride' => $rendered['body'],
            ]);
        } else {
            $mailer->send(
                $adminEmail,
                'Comprobante recibido — Pedido #' . $orderId . ' — VILUNA',
                'admin_notification',
                array_merge($variables, [
                    'titulo' => 'Comprobante de pago recibido',
                    'mensaje' => "Se ha recibido un comprobante para el pedido #{$orderId} de {$variables['nombre_cliente']}",
                ])
            );
        }
    }

    /**
     * Notifica al admin sobre una nueva reseña.
     *
     * @param int $reviewId
     */
    public static function adminNewReview(int $reviewId): void
    {
        $adminEmail = self::getAdminEmail();
        if (!$adminEmail) {
            return;
        }

        $review = ReviewModel::findById($reviewId);
        if (!$review) {
            return;
        }

        $product = ProductModel::findById((int)($review['producto_id'] ?? 0));

        $variables = [
            'nombre_cliente'    => ($review['usuario_nombre'] ?? '') . ' ' . ($review['usuario_apellido'] ?? ''),
            'producto_nombre'   => $product['nombre'] ?? 'Producto desconocido',
            'calificacion'      => (int)($review['calificacion'] ?? 0),
            'comentario'        => $review['comentario'] ?? '',
            'fecha'             => $review['fecha_creacion'] ?? date('Y-m-d H:i:s'),
            'tienda_nombre'     => defined('APP_NAME') ? APP_NAME : 'VILUNA',
        ];

        $rendered = EmailTemplateModel::render('admin_new_review', $variables);

        $mailer = new Mailer();

        if ($rendered) {
            $mailer->send($adminEmail, $rendered['subject'], 'admin_notification', [
                'htmlOverride' => $rendered['body'],
            ]);
        } else {
            $mailer->send(
                $adminEmail,
                'Nueva reseña pendiente — VILUNA',
                'admin_notification',
                array_merge($variables, [
                    'titulo' => 'Nueva reseña pendiente de aprobación',
                    'mensaje' => "{$variables['nombre_cliente']} dejó una reseña ({$variables['calificacion']}★) en {$variables['producto_nombre']}",
                ])
            );
        }
    }

    /**
     * Obtiene el correo del administrador desde la configuración o constantes.
     */
    private static function getAdminEmail(): ?string
    {
        // Try from DB config first
        $email = ConfigModel::get('admin_email', '');

        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        // Fallback to MAIL_FROM_EMAIL constant (the store email)
        if (defined('MAIL_FROM_EMAIL') && filter_var(MAIL_FROM_EMAIL, FILTER_VALIDATE_EMAIL)) {
            return MAIL_FROM_EMAIL;
        }

        return null;
    }
}
