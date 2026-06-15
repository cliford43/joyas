<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\CategoryModel;

/**
 * CartController — Gestión del carrito de compras.
 */
class CartController extends Controller
{
    /** GET /carrito */
    public function index(): void
    {
        $summary       = CartModel::getSummary();
        $navCategorias = CategoryModel::findActive();

        $this->render('cart/index', [
            'pageTitle'    => 'Mi carrito',
            'metaRobots'   => 'noindex',
            'summary'      => $summary,
            'navCategorias'=> $navCategorias,
        ]);
    }

    /** POST /carrito/agregar */
    public function add(): void
    {
        $productId = (int)($_POST['producto_id'] ?? 0);
        $qty       = max(1, (int)($_POST['cantidad'] ?? 1));

        if ($productId <= 0) {
            $this->respondError('Producto inválido.');
            return;
        }

        $product = ProductModel::findById($productId);
        if (!$product || !(int)$product['activo']) {
            $this->respondError('Producto no encontrado.');
            return;
        }

        if ((int)$product['stock'] === 0) {
            $this->respondError('Este producto no tiene stock disponible.');
            return;
        }

        // Obtener imagen principal
        $img = ProductModel::getMainImage($productId);
        $product['imagen_principal'] = $img['ruta'] ?? null;

        CartModel::add($productId, $qty, $product);
        $summary = CartModel::getSummary();

        if ($this->isAjax()) {
            $this->json([
                'success'    => true,
                'message'    => 'Producto agregado al carrito.',
                'totalItems' => $summary['totalItems'],
                'total'      => $summary['total'],
            ]);
            return;
        }

        $this->flash('success', 'Producto agregado al carrito.');
        $this->redirect(url('carrito'));
    }

    /** POST /carrito/actualizar */
    public function update(): void
    {
        $productId = (int)($_POST['producto_id'] ?? 0);
        $qty       = (int)($_POST['cantidad']    ?? 0);

        $ok = CartModel::update($productId, $qty);

        if ($this->isAjax()) {
            $summary = CartModel::getSummary();
            if ($ok) {
                $this->json([
                    'success'         => true,
                    'subtotal'        => CartModel::getSubtotal(),
                    'couponDiscount'  => CartModel::getCouponDiscount(),
                    'total'           => CartModel::calculateTotal(),
                    'totalItems'      => $summary['totalItems'],
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Cantidad fuera del rango permitido.'], 422);
            }
            return;
        }

        $this->redirect(url('carrito'));
    }

    /** POST /carrito/eliminar */
    public function remove(): void
    {
        $productId = (int)($_POST['producto_id'] ?? 0);
        CartModel::remove($productId);

        if ($this->isAjax()) {
            $summary = CartModel::getSummary();
            $this->json([
                'success'    => true,
                'totalItems' => $summary['totalItems'],
                'subtotal'   => $summary['subtotal'],
                'couponDiscount' => $summary['couponDiscount'],
                'total'      => $summary['total'],
            ]);
            return;
        }

        $this->flash('success', 'Producto eliminado del carrito.');
        $this->redirect(url('carrito'));
    }

    /** POST /carrito/vaciar */
    public function clear(): void
    {
        CartModel::clear();

        if ($this->isAjax()) {
            $this->json(['success' => true, 'totalItems' => 0, 'total' => 0]);
            return;
        }

        $this->flash('success', 'Carrito vaciado.');
        $this->redirect(url('carrito'));
    }

    private function respondError(string $message): void
    {
        if ($this->isAjax()) {
            $this->json(['success' => false, 'message' => $message], 422);
            return;
        }
        $this->flash('error', $message);
        $this->redirect(url('catalogo'));
    }
}
