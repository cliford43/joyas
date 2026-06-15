<?php

namespace App\Models;

use Core\Model;

/**
 * WishlistModel — Lista de deseos del cliente.
 */
class WishlistModel extends Model
{
    /**
     * Toggle: agrega si no existe, elimina si ya existe.
     * Retorna true si fue agregado, false si fue eliminado.
     */
    public static function toggle(int $userId, int $productId): bool
    {
        if (static::exists($userId, $productId)) {
            static::execute(
                'DELETE FROM wishlist WHERE usuario_id = :uid AND producto_id = :pid',
                [':uid' => $userId, ':pid' => $productId]
            );
            return false;
        }

        static::execute(
            'INSERT INTO wishlist (usuario_id, producto_id) VALUES (:uid, :pid)',
            [':uid' => $userId, ':pid' => $productId]
        );
        return true;
    }

    /** Retorna todos los productos guardados por el usuario con datos completos. */
    public static function findByUser(int $userId): array
    {
        return static::fetchAll(
            'SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug, w.fecha_agregado
             FROM wishlist w
             JOIN productos p ON p.id = w.producto_id
             JOIN categorias c ON c.id = p.categoria_id
             WHERE w.usuario_id = :uid
             ORDER BY w.fecha_agregado DESC',
            [':uid' => $userId]
        );
    }

    /** Verifica si un producto está en la wishlist del usuario. */
    public static function exists(int $userId, int $productId): bool
    {
        $row = static::fetchOne(
            'SELECT COUNT(*) AS n FROM wishlist WHERE usuario_id = :uid AND producto_id = :pid',
            [':uid' => $userId, ':pid' => $productId]
        );
        return (int)($row['n'] ?? 0) > 0;
    }

    /** Retorna los IDs de productos en la wishlist del usuario. */
    public static function getProductIds(int $userId): array
    {
        $rows = static::fetchAll(
            'SELECT producto_id FROM wishlist WHERE usuario_id = :uid',
            [':uid' => $userId]
        );
        return array_column($rows, 'producto_id');
    }
}
