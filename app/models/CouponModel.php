<?php

namespace App\Models;

use Core\Model;

/**
 * CouponModel — Gestión de cupones de descuento.
 */
class CouponModel extends Model
{
    /** Busca un cupón por código. */
    public static function findByCode(string $code): ?array
    {
        return static::fetchOne(
            'SELECT * FROM cupones WHERE codigo = :code LIMIT 1',
            [':code' => strtoupper(trim($code))]
        );
    }

    /** Busca un cupón por ID. */
    public static function findById(int $id): ?array
    {
        return static::fetchOne(
            'SELECT * FROM cupones WHERE id = :id LIMIT 1',
            [':id' => $id]
        );
    }

    /** Retorna todos los cupones. */
    public static function findAll(): array
    {
        return static::fetchAll(
            'SELECT * FROM cupones ORDER BY fecha_expiracion DESC'
        );
    }

    /**
     * Valida un cupón verificando las cuatro condiciones:
     * 1. Existencia del código
     * 2. activo = true
     * 3. fecha_expiracion > NOW()
     * 4. usos_actuales < limite_usos
     *
     * @return array{valid: bool, message: string, coupon: array|null}
     */
    public static function isValid(string $code): array
    {
        $coupon = static::findByCode($code);

        if (!$coupon) {
            return ['valid' => false, 'message' => 'El código de cupón no existe.', 'coupon' => null];
        }

        if (!(int)$coupon['activo']) {
            return ['valid' => false, 'message' => 'Este cupón no está activo.', 'coupon' => null];
        }

        if (strtotime($coupon['fecha_expiracion']) < time()) {
            return ['valid' => false, 'message' => 'Este cupón ha expirado.', 'coupon' => null];
        }

        if ((int)$coupon['usos_actuales'] >= (int)$coupon['limite_usos']) {
            return ['valid' => false, 'message' => 'Este cupón ha alcanzado su límite de usos.', 'coupon' => null];
        }

        return ['valid' => true, 'message' => 'Cupón aplicado correctamente.', 'coupon' => $coupon];
    }

    /**
     * Incrementa el contador de usos en exactamente 1.
     * Se llama al confirmar una orden con cupón.
     */
    public static function incrementUsage(int $couponId): void
    {
        static::execute(
            'UPDATE cupones SET usos_actuales = usos_actuales + 1 WHERE id = :id',
            [':id' => $couponId]
        );
    }

    /** Crea un nuevo cupón. */
    public static function create(array $data): int
    {
        static::execute(
            'INSERT INTO cupones (codigo, porcentaje, fecha_expiracion, limite_usos, activo)
             VALUES (:codigo, :porcentaje, :expiracion, :limite, :activo)',
            [
                ':codigo'     => strtoupper(trim($data['codigo'])),
                ':porcentaje' => (float)$data['porcentaje'],
                ':expiracion' => $data['fecha_expiracion'],
                ':limite'     => (int)($data['limite_usos'] ?? 100),
                ':activo'     => isset($data['activo']) ? (int)$data['activo'] : 1,
            ]
        );
        return (int)static::lastInsertId();
    }

    /** Actualiza un cupón. */
    public static function update(int $id, array $data): void
    {
        static::execute(
            'UPDATE cupones SET codigo = :codigo, porcentaje = :porcentaje,
             fecha_expiracion = :expiracion, limite_usos = :limite, activo = :activo
             WHERE id = :id',
            [
                ':codigo'     => strtoupper(trim($data['codigo'])),
                ':porcentaje' => (float)$data['porcentaje'],
                ':expiracion' => $data['fecha_expiracion'],
                ':limite'     => (int)($data['limite_usos'] ?? 100),
                ':activo'     => isset($data['activo']) ? (int)$data['activo'] : 1,
                ':id'         => $id,
            ]
        );
    }

    /** Alterna estado activo/inactivo. */
    public static function toggleStatus(int $id): void
    {
        static::execute(
            'UPDATE cupones SET activo = IF(activo = 1, 0, 1) WHERE id = :id',
            [':id' => $id]
        );
    }
}
