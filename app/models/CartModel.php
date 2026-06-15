<?php

namespace App\Models;

/**
 * CartModel — Gestión del carrito de compras en sesión.
 * El carrito se almacena en $_SESSION['cart'] como array indexado por producto_id.
 * No persiste en base de datos.
 */
class CartModel
{
    private const SESSION_KEY = 'cart';
    private const COUPON_KEY  = 'cart_cupon';

    // ─── Lectura ──────────────────────────────────────────────

    /** Retorna todos los ítems del carrito. */
    public static function getItems(): array
    {
        return $_SESSION[self::SESSION_KEY] ?? [];
    }

    /** Retorna la cantidad total de ítems (suma de cantidades). */
    public static function getTotalItems(): int
    {
        return array_sum(array_column(self::getItems(), 'cantidad'));
    }

    /** Verifica si el carrito está vacío. */
    public static function isEmpty(): bool
    {
        return empty($_SESSION[self::SESSION_KEY]);
    }

    /** Retorna el cupón aplicado o null. */
    public static function getCoupon(): ?array
    {
        return $_SESSION[self::COUPON_KEY] ?? null;
    }

    // ─── Escritura ────────────────────────────────────────────

    /**
     * Agrega un producto al carrito o incrementa la cantidad.
     * Limita la cantidad al stock disponible.
     *
     * @param int   $productId  ID del producto
     * @param int   $qty        Cantidad a agregar
     * @param array $productData Datos del producto (nombre, precio, descuento, stock, imagen)
     */
    public static function add(int $productId, int $qty, array $productData): array
    {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }

        $stock = (int)($productData['stock'] ?? 0);
        $qty   = max(1, $qty);

        if (isset($_SESSION[self::SESSION_KEY][$productId])) {
            $newQty = $_SESSION[self::SESSION_KEY][$productId]['cantidad'] + $qty;
            $newQty = min($newQty, $stock);
            $_SESSION[self::SESSION_KEY][$productId]['cantidad'] = $newQty;
        } else {
            $qty = min($qty, $stock);
            $_SESSION[self::SESSION_KEY][$productId] = [
                'producto_id'      => $productId,
                'nombre'           => $productData['nombre']    ?? '',
                'precio'           => (float)($productData['precio']    ?? 0),
                'descuento'        => (float)($productData['descuento'] ?? 0),
                'stock'            => $stock,
                'imagen_principal' => $productData['imagen_principal'] ?? null,
                'slug'             => $productData['slug'] ?? '',
                'cantidad'         => $qty,
            ];
        }

        return self::getItems();
    }

    /**
     * Actualiza la cantidad de un ítem.
     * Rechaza cantidades fuera del rango [1, stock].
     *
     * @return bool true si se actualizó, false si la cantidad es inválida
     */
    public static function update(int $productId, int $qty): bool
    {
        if (!isset($_SESSION[self::SESSION_KEY][$productId])) {
            return false;
        }

        $stock = (int)$_SESSION[self::SESSION_KEY][$productId]['stock'];

        if ($qty < 1 || $qty > $stock) {
            return false;
        }

        $_SESSION[self::SESSION_KEY][$productId]['cantidad'] = $qty;
        return true;
    }

    /** Elimina un ítem del carrito por ID de producto. */
    public static function remove(int $productId): void
    {
        unset($_SESSION[self::SESSION_KEY][$productId]);
    }

    /** Vacía el carrito completo y quita el cupón. */
    public static function clear(): void
    {
        $_SESSION[self::SESSION_KEY] = [];
        unset($_SESSION[self::COUPON_KEY]);
    }

    /** Aplica un cupón de descuento al carrito. */
    public static function applyCoupon(array $coupon): void
    {
        $_SESSION[self::COUPON_KEY] = [
            'id'         => $coupon['id'],
            'codigo'     => $coupon['codigo'],
            'porcentaje' => (float)$coupon['porcentaje'],
        ];
    }

    /** Quita el cupón aplicado. */
    public static function removeCoupon(): void
    {
        unset($_SESSION[self::COUPON_KEY]);
    }

    // ─── Cálculos ─────────────────────────────────────────────

    /**
     * Calcula el subtotal (sin descuento de cupón).
     * subtotal = sum((precio - descuento_unitario) * cantidad)
     * Nunca retorna negativo.
     */
    public static function getSubtotal(): float
    {
        $subtotal = 0.0;
        foreach (self::getItems() as $item) {
            $precioItem = (float)$item['precio'] - (float)$item['descuento'];
            $precioItem = max(0.0, $precioItem);
            $subtotal  += $precioItem * (int)$item['cantidad'];
        }
        return round(max(0.0, $subtotal), 2);
    }

    /**
     * Calcula el descuento del cupón sobre el subtotal.
     */
    public static function getCouponDiscount(): float
    {
        $coupon = self::getCoupon();
        if (!$coupon) return 0.0;
        $pct = (float)($coupon['porcentaje'] ?? 0);
        return round(self::getSubtotal() * ($pct / 100), 2);
    }

    /**
     * Calcula el total final (subtotal - descuento cupón).
     * Nunca retorna negativo.
     */
    public static function calculateTotal(): float
    {
        return round(max(0.0, self::getSubtotal() - self::getCouponDiscount()), 2);
    }

    /**
     * Retorna un resumen completo del carrito.
     */
    public static function getSummary(): array
    {
        return [
            'items'           => self::getItems(),
            'totalItems'      => self::getTotalItems(),
            'subtotal'        => self::getSubtotal(),
            'coupon'          => self::getCoupon(),
            'couponDiscount'  => self::getCouponDiscount(),
            'total'           => self::calculateTotal(),
        ];
    }
}
