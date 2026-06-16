<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\CouponModel;
use App\Models\CartModel;

/**
 * CouponController — Aplicar/quitar cupones de descuento en el carrito.
 */
class CouponController extends Controller
{
    /** POST /cupon/aplicar */
    public function apply(): void
    {
        $code   = strtoupper(trim($_POST['codigo'] ?? ''));
        $result = CouponModel::isValid($code);

        if ($this->isAjax()) {
            if ($result['valid']) {
                CartModel::applyCoupon($result['coupon']);
                $this->json([
                    'success'        => true,
                    'message'        => $result['message'],
                    'codigo'         => $result['coupon']['codigo'],
                    'porcentaje'     => $result['coupon']['porcentaje'],
                    'subtotal'       => CartModel::getSubtotal(),
                    'couponDiscount' => CartModel::getCouponDiscount(),
                    'total'          => CartModel::calculateTotal(),
                    'totalItems'     => CartModel::getTotalItems(),
                ]);
            } else {
                $this->json(['success' => false, 'message' => $result['message']], 422);
            }
            return;
        }

        if ($result['valid']) {
            CartModel::applyCoupon($result['coupon']);
            $this->flash('success', $result['message']);
        } else {
            $this->flash('error', $result['message']);
        }
        $this->redirect(url('carrito'));
    }

    /** POST /cupon/quitar */
    public function remove(): void
    {
        CartModel::removeCoupon();

        if ($this->isAjax()) {
            $this->json([
                'success' => true,
                'total'   => CartModel::calculateTotal(),
            ]);
            return;
        }
        $this->redirect(url('carrito'));
    }
}
