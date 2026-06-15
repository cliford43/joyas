<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\CartModel;
use App\Models\OrderModel;
use App\Models\UserModel;
use App\Models\CouponModel;
use App\Models\CategoryModel;
use App\Models\ConfigModel;
use Services\FileUploader;
use Services\Mailer;

/**
 * CheckoutController — Proceso de compra: dirección, pago, confirmación.
 */
class CheckoutController extends Controller
{
    /** GET /checkout */
    public function index(): void
    {
        if (CartModel::isEmpty()) {
            $this->flash('error', 'Tu carrito está vacío.');
            $this->redirect(url('catalogo'));
            return;
        }

        $user          = UserModel::findById($this->userId());
        $summary       = CartModel::getSummary();
        $navCategorias = CategoryModel::findActive();
        $config        = ConfigModel::getAll();

        $this->render('checkout/index', [
            'pageTitle'     => 'Finalizar compra',
            'metaRobots'    => 'noindex',
            'summary'       => $summary,
            'user'          => $user,
            'config'        => $config,
            'navCategorias' => $navCategorias,
        ]);
    }

    /** POST /checkout/subir-comprobante */
    public function uploadVoucher(): void
    {
        if (empty($_FILES['comprobante']) || $_FILES['comprobante']['error'] === UPLOAD_ERR_NO_FILE) {
            $this->json(['success' => false, 'message' => 'No se seleccionó ningún archivo.'], 422);
            return;
        }

        try {
            $destDir = defined('UPLOAD_PATH') ? UPLOAD_PATH . '/comprobantes' : ROOT_PATH . '/uploads/comprobantes';
            $ruta    = FileUploader::uploadVoucher($_FILES['comprobante'], $destDir);
            $_SESSION['checkout_comprobante'] = $ruta;
            $this->json(['success' => true, 'ruta' => $ruta]);
        } catch (\InvalidArgumentException $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /** POST /checkout/confirmar */
    public function confirm(): void
    {
        if (CartModel::isEmpty()) {
            $this->flash('error', 'Tu carrito está vacío.');
            $this->redirect(url('carrito'));
            return;
        }

        $metodoPago = $_POST['metodo_pago']      ?? '';
        $direccion  = trim($_POST['direccion']   ?? '');

        $errors = [];
        if (!in_array($metodoPago, ['contra_entrega', 'transferencia'])) {
            $errors[] = 'Selecciona un método de pago válido.';
        }
        if (mb_strlen($direccion) < 10) {
            $errors[] = 'La dirección de entrega debe tener al menos 10 caracteres.';
        }

        $comprobanteRuta = null;
        if ($metodoPago === 'transferencia') {
            // Verificar comprobante: puede venir del POST (subida en este request) o de sesión previa
            if (!empty($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
                try {
                    $destDir = defined('UPLOAD_PATH') ? UPLOAD_PATH . '/comprobantes' : ROOT_PATH . '/uploads/comprobantes';
                    $comprobanteRuta = FileUploader::uploadVoucher($_FILES['comprobante'], $destDir);
                    $_SESSION['checkout_comprobante'] = $comprobanteRuta;
                } catch (\InvalidArgumentException $e) {
                    $errors[] = $e->getMessage();
                }
            } elseif (!empty($_SESSION['checkout_comprobante'])) {
                $comprobanteRuta = $_SESSION['checkout_comprobante'];
            } else {
                $errors[] = 'Debes subir el comprobante de transferencia.';
            }
        }

        if (!empty($errors)) {
            $user          = UserModel::findById($this->userId());
            $summary       = CartModel::getSummary();
            $navCategorias = CategoryModel::findActive();
            $config        = ConfigModel::getAll();
            $this->render('checkout/index', [
                'pageTitle'     => 'Finalizar compra',
                'metaRobots'    => 'noindex',
                'summary'       => $summary,
                'user'          => $user,
                'config'        => $config,
                'navCategorias' => $navCategorias,
                'errors'        => $errors,
                'old'           => ['metodo_pago' => $metodoPago, 'direccion' => $direccion],
            ]);
            return;
        }

        $summary  = CartModel::getSummary();
        $coupon   = $summary['coupon'];

        try {
            $orderId = OrderModel::createFromCart(
                $this->userId(),
                [
                    'metodo_pago'      => $metodoPago,
                    'direccion_entrega'=> $direccion,
                    'subtotal'         => $summary['subtotal'],
                    'descuento_cupon'  => $summary['couponDiscount'],
                    'total'            => $summary['total'],
                    'cupon_id'         => $coupon ? (int)$coupon['id'] : null,
                    'comprobante_ruta' => $comprobanteRuta,
                ],
                $summary['items']
            );
        } catch (\RuntimeException $e) {
            $this->flash('error', 'Error al procesar la orden. Intenta de nuevo.');
            $this->redirect(url('checkout'));
            return;
        }

        // Incrementar uso de cupón
        if ($coupon) {
            CouponModel::incrementUsage((int)$coupon['id']);
        }

        // Enviar correo de confirmación
        $user   = UserModel::findById($this->userId());
        $items  = OrderModel::getDetails($orderId);
        $mailer = new Mailer();
        $mailer->send(
            $user['correo'],
            'Orden confirmada #' . $orderId . ' — VILUNA',
            'order_confirmation',
            [
                'nombre'    => $user['nombre'],
                'ordenId'   => $orderId,
                'items'     => $items,
                'total'     => $summary['total'],
                'metodoPago'=> $metodoPago === 'contra_entrega' ? 'Contra entrega' : 'Transferencia bancaria',
                'direccion' => $direccion,
            ]
        );

        // Limpiar carrito y sesión de checkout
        CartModel::clear();
        unset($_SESSION['checkout_comprobante']);

        $this->redirect(url('checkout/confirmacion/' . $orderId));
    }

    /** GET /checkout/confirmacion/{orderId} */
    public function confirmation(string $orderId): void
    {
        $order = OrderModel::findById((int)$orderId);

        if (!$order || (int)$order['usuario_id'] !== $this->userId()) {
            (new ErrorController())->notFound();
            return;
        }

        $items         = OrderModel::getDetails((int)$orderId);
        $navCategorias = CategoryModel::findActive();

        $this->render('checkout/confirmation', [
            'pageTitle'     => 'Orden confirmada',
            'metaRobots'    => 'noindex',
            'order'         => $order,
            'items'         => $items,
            'navCategorias' => $navCategorias,
        ]);
    }
}
