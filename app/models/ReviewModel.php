<?php

namespace App\Models;

use Core\Model;

/**
 * ReviewModel — Gestión de reseñas de productos VILUNA.
 */
class ReviewModel extends Model
{
    // ─── Validación ───────────────────────────────────────────

    /**
     * Valida los datos de una reseña.
     * Retorna array de errores (vacío si todo es válido).
     */
    public static function validate(array $data): array
    {
        $errors = [];

        // Calificación: entero entre 1 y 5
        $rating = $data['calificacion'] ?? null;
        if ($rating === null || $rating === '') {
            $errors['calificacion'] = 'La calificación es obligatoria.';
        } else {
            $rating = (int)$rating;
            if ($rating < 1 || $rating > 5) {
                $errors['calificacion'] = 'La calificación debe ser entre 1 y 5 estrellas.';
            }
        }

        // Comentario: entre 10 y 1000 caracteres
        $comment = $data['comentario'] ?? '';
        $length = mb_strlen(trim($comment));
        if ($length < 10) {
            $errors['comentario'] = 'El comentario debe tener al menos 10 caracteres.';
        } elseif ($length > 1000) {
            $errors['comentario'] = 'El comentario no puede exceder 1000 caracteres.';
        }

        return $errors;
    }

    // ─── Sanitización ─────────────────────────────────────────

    /**
     * Sanitiza el texto del comentario eliminando HTML y scripts.
     */
    public static function sanitize(string $text): string
    {
        $text = strip_tags($text);
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        return trim($text);
    }

    // ─── Creación ─────────────────────────────────────────────

    /**
     * Crea una nueva reseña. Sanitiza el comentario antes de guardar.
     * Retorna el ID de la reseña creada.
     */
    public static function create(array $data): int
    {
        $comentario = self::sanitize($data['comentario']);

        static::execute(
            'INSERT INTO resenas (usuario_id, producto_id, calificacion, comentario, estado, ip_address)
             VALUES (:usuario_id, :producto_id, :calificacion, :comentario, :estado, :ip)',
            [
                ':usuario_id'   => (int)$data['usuario_id'],
                ':producto_id'  => (int)$data['producto_id'],
                ':calificacion' => (int)$data['calificacion'],
                ':comentario'   => $comentario,
                ':estado'       => 'pendiente',
                ':ip'           => $data['ip_address'] ?? null,
            ]
        );

        return (int)static::lastInsertId();
    }

    // ─── Consultas ────────────────────────────────────────────

    /**
     * Retorna una reseña por ID.
     */
    public static function findById(int $id): ?array
    {
        return static::fetchOne(
            'SELECT r.*, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido
             FROM resenas r
             JOIN usuarios u ON u.id = r.usuario_id
             WHERE r.id = :id LIMIT 1',
            [':id' => $id]
        );
    }

    /**
     * Verifica si un usuario ya tiene una reseña para un producto.
     */
    public static function userHasReview(int $userId, int $productId): bool
    {
        $row = static::fetchOne(
            'SELECT COUNT(*) AS n FROM resenas
             WHERE usuario_id = :uid AND producto_id = :pid',
            [':uid' => $userId, ':pid' => $productId]
        );
        return (int)($row['n'] ?? 0) > 0;
    }

    /**
     * Cuenta reseñas del usuario en las últimas 24 horas (rate limit).
     */
    public static function countRecentByUser(int $userId): int
    {
        $row = static::fetchOne(
            'SELECT COUNT(*) AS n FROM resenas
             WHERE usuario_id = :uid
               AND fecha_creacion >= DATE_SUB(NOW(), INTERVAL 24 HOUR)',
            [':uid' => $userId]
        );
        return (int)($row['n'] ?? 0);
    }

    /**
     * Retorna las reseñas aprobadas de un producto, ordenadas por fecha descendente.
     */
    public static function getApprovedByProduct(int $productId): array
    {
        return static::fetchAll(
            "SELECT r.*, u.nombre AS usuario_nombre
             FROM resenas r
             JOIN usuarios u ON u.id = r.usuario_id
             WHERE r.producto_id = :pid AND r.estado = 'aprobado'
             ORDER BY r.fecha_creacion DESC",
            [':pid' => $productId]
        );
    }

    /**
     * Calcula estadísticas de reseñas aprobadas para un producto.
     * Retorna: ['promedio' => float, 'total_valoraciones' => int, 'total_comentarios' => int]
     */
    public static function getProductStats(int $productId): array
    {
        $row = static::fetchOne(
            "SELECT
                COALESCE(AVG(calificacion), 0) AS promedio,
                COUNT(*) AS total_valoraciones,
                COUNT(*) AS total_comentarios
             FROM resenas
             WHERE producto_id = :pid AND estado = 'aprobado'",
            [':pid' => $productId]
        );

        return [
            'promedio'            => round((float)($row['promedio'] ?? 0), 1),
            'total_valoraciones'  => (int)($row['total_valoraciones'] ?? 0),
            'total_comentarios'   => (int)($row['total_comentarios'] ?? 0),
        ];
    }

    // ─── Admin: Listado con Filtros ───────────────────────────────────────

    /**
     * Retorna reseñas filtradas con paginación para el panel admin.
     */
    public static function findFiltered(array $filters, int $limit = 20, int $offset = 0): array
    {
        $where = [];
        $params = [];

        self::buildFilterConditions($filters, $where, $params);

        $sql = "SELECT r.*, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido,
                       p.nombre AS producto_nombre
                FROM resenas r
                JOIN usuarios u ON u.id = r.usuario_id
                JOIN productos p ON p.id = r.producto_id"
             . (count($where) ? ' WHERE ' . implode(' AND ', $where) : '')
             . " ORDER BY r.fecha_creacion DESC LIMIT :limit OFFSET :offset";

        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        return static::fetchAll($sql, $params);
    }

    /**
     * Cuenta reseñas filtradas (para paginación admin).
     */
    public static function countFiltered(array $filters): int
    {
        $where = [];
        $params = [];

        self::buildFilterConditions($filters, $where, $params);

        $sql = "SELECT COUNT(*) AS n FROM resenas r"
             . (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

        $row = static::fetchOne($sql, $params);
        return (int)($row['n'] ?? 0);
    }

    /**
     * Construye condiciones WHERE a partir de filtros admin.
     */
    private static function buildFilterConditions(array $filters, array &$where, array &$params): void
    {
        if (!empty($filters['producto_id'])) {
            $where[] = 'r.producto_id = :producto_id';
            $params[':producto_id'] = (int)$filters['producto_id'];
        }

        if (!empty($filters['usuario_id'])) {
            $where[] = 'r.usuario_id = :usuario_id';
            $params[':usuario_id'] = (int)$filters['usuario_id'];
        }

        if (!empty($filters['estado'])) {
            $where[] = 'r.estado = :estado';
            $params[':estado'] = $filters['estado'];
        }

        if (!empty($filters['calificacion'])) {
            $where[] = 'r.calificacion = :calificacion';
            $params[':calificacion'] = (int)$filters['calificacion'];
        }

        if (!empty($filters['fecha_desde'])) {
            $where[] = 'r.fecha_creacion >= :fecha_desde';
            $params[':fecha_desde'] = $filters['fecha_desde'] . ' 00:00:00';
        }

        if (!empty($filters['fecha_hasta'])) {
            $where[] = 'r.fecha_creacion <= :fecha_hasta';
            $params[':fecha_hasta'] = $filters['fecha_hasta'] . ' 23:59:59';
        }
    }

    // ─── Admin: Acciones de Moderación ────────────────────────────────────

    /**
     * Aprueba una reseña (cambia estado a 'aprobado').
     */
    public static function approve(int $id): void
    {
        static::execute(
            "UPDATE resenas SET estado = 'aprobado' WHERE id = :id",
            [':id' => $id]
        );
    }

    /**
     * Rechaza una reseña (cambia estado a 'rechazado').
     */
    public static function reject(int $id): void
    {
        static::execute(
            "UPDATE resenas SET estado = 'rechazado' WHERE id = :id",
            [':id' => $id]
        );
    }

    /**
     * Elimina permanentemente una reseña.
     */
    public static function delete(int $id): void
    {
        static::execute(
            "DELETE FROM resenas WHERE id = :id",
            [':id' => $id]
        );
    }

    /**
     * Actualiza solo el texto del comentario de una reseña.
     */
    public static function updateComment(int $id, string $text): void
    {
        $sanitized = self::sanitize($text);
        static::execute(
            "UPDATE resenas SET comentario = :comentario WHERE id = :id",
            [':comentario' => $sanitized, ':id' => $id]
        );
    }

    // ─── Home Sections ────────────────────────────────────────

    /**
     * Retorna IDs de productos ordenados por calificación promedio (solo aprobadas), descendente.
     */
    public static function getTopRatedProductIds(int $limit = 8): array
    {
        return static::fetchAll(
            "SELECT r.producto_id, AVG(r.calificacion) AS promedio
             FROM resenas r
             WHERE r.estado = 'aprobado'
             GROUP BY r.producto_id
             HAVING COUNT(*) >= 1
             ORDER BY promedio DESC
             LIMIT :lim",
            [':lim' => $limit]
        );
    }

    /**
     * Retorna IDs de productos ordenados por cantidad de reseñas aprobadas, descendente.
     */
    public static function getMostReviewedProductIds(int $limit = 8): array
    {
        return static::fetchAll(
            "SELECT r.producto_id, COUNT(*) AS total_resenas
             FROM resenas r
             WHERE r.estado = 'aprobado'
             GROUP BY r.producto_id
             ORDER BY total_resenas DESC
             LIMIT :lim",
            [':lim' => $limit]
        );
    }

    /**
     * Retorna testimonios destacados: reseñas aprobadas con calificación >= 4.
     */
    public static function getTestimonials(int $limit = 6): array
    {
        return static::fetchAll(
            "SELECT r.*, u.nombre AS usuario_nombre, p.nombre AS producto_nombre, p.slug AS producto_slug
             FROM resenas r
             JOIN usuarios u ON u.id = r.usuario_id
             JOIN productos p ON p.id = r.producto_id
             WHERE r.estado = 'aprobado' AND r.calificacion >= 4 AND p.activo = 1
             ORDER BY r.calificacion DESC, r.fecha_creacion DESC
             LIMIT :lim",
            [':lim' => $limit]
        );
    }
}
