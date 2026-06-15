<?php

namespace App\Models;

use Core\Model;

/**
 * OrderModel — Gestión de órdenes de compra VILUNA.
 */
class OrderModel extends Model
{
    // Estados válidos de una orden
    public const ESTADOS = [
        'pendiente'       => 'Pendiente',
        'pagada'          => 'Pagada',
        'en_preparacion'  => 'En preparación',
        'enviada'         => 'Enviada',
        'entregada'       => 'Entregada',
        'cancelada'       => 'Cancelada',
    ];

    // ─── Creación ─────────────────────────────────────────────

    /**
     * Crea una nueva orden y sus detalles en una transacción.
     * Reduce el stock de cada producto.
     * Retorna el ID de la orden creada.
     *
     * @param int   $userId
     * @param array $data {
     *   metodo_pago: string,
     *   direccion_entrega: string,
     *   subtotal: float,
     *   descuento_cupon: float,
     *   total: float,
     *   cupon_id?: int|null,
     *   comprobante_ruta?: string|null,
     * }
     * @param array $items Ítems del carrito
     * @throws \RuntimeException si la transacción falla
     */
    public static function createFromCart(int $userId, array $data, array $items): int
    {
        static::beginTransaction();

        try {
            // Insertar orden
            static::execute(
                'INSERT INTO ordenes
                 (usuario_id, cupon_id, metodo_pago, estado, subtotal, descuento_cupon, total,
                  direccion_entrega, comprobante_ruta)
                 VALUES (:uid, :cupon, :metodo, "pendiente", :subtotal, :desc_cupon, :total,
                         :direccion, :comprobante)',
                [
                    ':uid'         => $userId,
                    ':cupon'       => $data['cupon_id']        ?? null,
                    ':metodo'      => $data['metodo_pago'],
                    ':subtotal'    => (float)($data['subtotal']       ?? 0),
                    ':desc_cupon'  => (float)($data['descuento_cupon'] ?? 0),
                    ':total'       => (float)($data['total']           ?? 0),
                    ':direccion'   => $data['direccion_entrega'],
                    ':comprobante' => $data['comprobante_ruta'] ?? null,
                ]
            );

            $orderId = (int)static::lastInsertId();

            // Insertar detalles y reducir stock
            foreach ($items as $item) {
                $precio    = (float)$item['precio'];
                $descuento = (float)($item['descuento'] ?? 0);
                $cantidad  = (int)$item['cantidad'];
                $productId = (int)$item['producto_id'];

                static::execute(
                    'INSERT INTO orden_detalle
                     (orden_id, producto_id, cantidad, precio_unitario, descuento_unitario)
                     VALUES (:oid, :pid, :qty, :precio, :descuento)',
                    [
                        ':oid'      => $orderId,
                        ':pid'      => $productId,
                        ':qty'      => $cantidad,
                        ':precio'   => $precio,
                        ':descuento'=> $descuento,
                    ]
                );

                // Reducir stock (nunca negativo)
                static::execute(
                    'UPDATE productos SET stock = GREATEST(0, stock - :qty) WHERE id = :id',
                    [':qty' => $cantidad, ':id' => $productId]
                );
            }

            static::commit();
            return $orderId;

        } catch (\Throwable $e) {
            static::rollback();
            throw new \RuntimeException('Error al crear la orden: ' . $e->getMessage());
        }
    }

    // ─── Consultas ────────────────────────────────────────────

    /** Busca una orden por ID con datos del usuario. */
    public static function findById(int $id): ?array
    {
        return static::fetchOne(
            'SELECT o.*, u.nombre, u.apellido, u.correo
             FROM ordenes o
             JOIN usuarios u ON u.id = o.usuario_id
             WHERE o.id = :id LIMIT 1',
            [':id' => $id]
        );
    }

    /** Retorna las órdenes de un usuario ordenadas por fecha desc. */
    public static function findByUser(int $userId): array
    {
        return static::fetchAll(
            'SELECT * FROM ordenes WHERE usuario_id = :uid ORDER BY fecha_creacion DESC',
            [':uid' => $userId]
        );
    }

    /** Retorna todas las órdenes con filtro opcional por estado. */
    public static function findAll(string $estado = '', int $limit = 50, int $offset = 0): array
    {
        if ($estado !== '') {
            return static::fetchAll(
                'SELECT o.*, u.nombre, u.apellido, u.correo
                 FROM ordenes o
                 JOIN usuarios u ON u.id = o.usuario_id
                 WHERE o.estado = :estado
                 ORDER BY o.fecha_creacion DESC
                 LIMIT :lim OFFSET :off',
                [':estado' => $estado, ':lim' => $limit, ':off' => $offset]
            );
        }

        return static::fetchAll(
            'SELECT o.*, u.nombre, u.apellido, u.correo
             FROM ordenes o
             JOIN usuarios u ON u.id = o.usuario_id
             ORDER BY o.fecha_creacion DESC
             LIMIT :lim OFFSET :off',
            [':lim' => $limit, ':off' => $offset]
        );
    }

    /** Retorna los detalles de una orden con datos del producto. */
    public static function getDetails(int $orderId): array
    {
        return static::fetchAll(
            'SELECT od.*, p.nombre, p.slug
             FROM orden_detalle od
             JOIN productos p ON p.id = od.producto_id
             WHERE od.orden_id = :id',
            [':id' => $orderId]
        );
    }

    /** Retorna comprobantes pendientes de revisión (transferencias). */
    public static function getPendingVouchers(): array
    {
        return static::fetchAll(
            'SELECT o.*, u.nombre, u.apellido, u.correo
             FROM ordenes o
             JOIN usuarios u ON u.id = o.usuario_id
             WHERE o.metodo_pago = "transferencia"
               AND o.comprobante_ruta IS NOT NULL
               AND o.estado = "pendiente"
             ORDER BY o.fecha_creacion ASC'
        );
    }

    /** Cuenta órdenes por estado (para estadísticas). */
    public static function countByStatus(): array
    {
        return static::fetchAll(
            'SELECT estado, COUNT(*) AS total FROM ordenes GROUP BY estado'
        );
    }

    /** Suma total de ventas entregadas. */
    public static function getTotalSales(): float
    {
        $row = static::fetchOne(
            'SELECT COALESCE(SUM(total), 0) AS total FROM ordenes WHERE estado = "entregada"'
        );
        return (float)($row['total'] ?? 0);
    }

    /**
     * Retorna ventas por mes de los últimos 12 meses.
     * Siempre retorna exactamente 12 entradas (0 para meses sin ventas).
     */
    public static function getMonthlySales(): array
    {
        $rows = static::fetchAll(
            "SELECT DATE_FORMAT(fecha_creacion, '%Y-%m') AS mes,
                    SUM(total) AS total
             FROM ordenes
             WHERE estado != 'cancelada'
               AND fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY mes
             ORDER BY mes ASC"
        );

        // Construir mapa por mes
        $map = [];
        foreach ($rows as $row) {
            $map[$row['mes']] = (float)$row['total'];
        }

        // Rellenar los 12 meses (incluso los vacíos)
        $result = [];
        for ($i = 11; $i >= 0; $i--) {
            $key          = date('Y-m', strtotime("-{$i} months"));
            $result[$key] = $map[$key] ?? 0.0;
        }

        return $result;
    }

    // ─── Actualización ────────────────────────────────────────

    /** Actualiza el estado de una orden. */
    public static function updateStatus(int $orderId, string $estado): void
    {
        if (!array_key_exists($estado, self::ESTADOS)) {
            throw new \InvalidArgumentException("Estado inválido: {$estado}");
        }

        static::execute(
            'UPDATE ordenes SET estado = :estado WHERE id = :id',
            [':estado' => $estado, ':id' => $orderId]
        );
    }

    /** Guarda la ruta del comprobante de transferencia. */
    public static function saveVoucher(int $orderId, string $ruta): void
    {
        static::execute(
            'UPDATE ordenes SET comprobante_ruta = :ruta WHERE id = :id',
            [':ruta' => $ruta, ':id' => $orderId]
        );
    }
}
