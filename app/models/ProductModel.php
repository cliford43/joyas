<?php

namespace App\Models;

use Core\Model;

/**
 * ProductModel — Gestión del catálogo de productos VILUNA.
 */
class ProductModel extends Model
{
    // ─── Consultas básicas ────────────────────────────────────

    /** Retorna todos los productos con nombre de categoría. */
    public static function findAll(int $limit = 50, int $offset = 0): array
    {
        return static::fetchAll(
            'SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             JOIN categorias c ON c.id = p.categoria_id
             ORDER BY p.fecha_creacion DESC
             LIMIT :lim OFFSET :off',
            [':lim' => $limit, ':off' => $offset]
        );
    }

    /** Retorna producto por slug (solo activos). */
    public static function findBySlug(string $slug): ?array
    {
        return static::fetchOne(
            'SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
             FROM productos p
             JOIN categorias c ON c.id = p.categoria_id
             WHERE p.slug = :slug AND p.activo = 1
             LIMIT 1',
            [':slug' => $slug]
        );
    }

    /** Retorna producto por ID. */
    public static function findById(int $id): ?array
    {
        return static::fetchOne(
            'SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
             FROM productos p
             JOIN categorias c ON c.id = p.categoria_id
             WHERE p.id = :id LIMIT 1',
            [':id' => $id]
        );
    }

    /** Retorna productos activos de una categoría (por slug). */
    public static function findByCategory(string $categorySlug, int $limit = 24, int $offset = 0): array
    {
        return static::fetchAll(
            'SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
             FROM productos p
             JOIN categorias c ON c.id = p.categoria_id
             WHERE c.slug = :slug AND p.activo = 1
             ORDER BY p.fecha_creacion DESC
             LIMIT :lim OFFSET :off',
            [':slug' => $categorySlug, ':lim' => $limit, ':off' => $offset]
        );
    }

    /** Cuenta productos activos en una categoría. */
    public static function countByCategory(string $categorySlug): int
    {
        $row = static::fetchOne(
            'SELECT COUNT(*) AS n FROM productos p
             JOIN categorias c ON c.id = p.categoria_id
             WHERE c.slug = :slug AND p.activo = 1',
            [':slug' => $categorySlug]
        );
        return (int)($row['n'] ?? 0);
    }

    /** Cuenta total de productos activos. */
    public static function countActive(): int
    {
        $row = static::fetchOne('SELECT COUNT(*) AS n FROM productos WHERE activo = 1');
        return (int)($row['n'] ?? 0);
    }

    // ─── Secciones de la home ─────────────────────────────────

    /**
     * Retorna los productos más vendidos ordenados por cantidad vendida total.
     * Excluye órdenes canceladas.
     */
    public static function getBestsellers(int $limit = 8): array
    {
        return static::fetchAll(
            'SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug,
                    COALESCE(SUM(od.cantidad), 0) AS total_vendido
             FROM productos p
             JOIN categorias c ON c.id = p.categoria_id
             LEFT JOIN orden_detalle od ON od.producto_id = p.id
             LEFT JOIN ordenes o ON o.id = od.orden_id AND o.estado != "cancelada"
             WHERE p.activo = 1
             GROUP BY p.id
             ORDER BY total_vendido DESC, p.fecha_creacion DESC
             LIMIT :lim',
            [':lim' => $limit]
        );
    }

    /**
     * Retorna productos creados en los últimos 7 días naturales.
     */
    public static function getNew(int $limit = 8): array
    {
        return static::fetchAll(
            'SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
             FROM productos p
             JOIN categorias c ON c.id = p.categoria_id
             WHERE p.activo = 1
               AND p.fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             ORDER BY p.fecha_creacion DESC
             LIMIT :lim',
            [':lim' => $limit]
        );
    }

    /**
     * Retorna productos marcados como destacados.
     */
    public static function getFeatured(int $limit = 8): array
    {
        return static::fetchAll(
            'SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
             FROM productos p
             JOIN categorias c ON c.id = p.categoria_id
             WHERE p.activo = 1 AND p.destacado = 1
             ORDER BY p.fecha_creacion DESC
             LIMIT :lim',
            [':lim' => $limit]
        );
    }

    /**
     * Retorna hasta 4 productos relacionados de la misma categoría,
     * excluyendo el producto actual.
     */
    public static function getRelated(int $categoryId, int $excludeId, int $limit = 4): array
    {
        return static::fetchAll(
            'SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
             FROM productos p
             JOIN categorias c ON c.id = p.categoria_id
             WHERE p.activo = 1
               AND p.categoria_id = :cat
               AND p.id != :exclude
             ORDER BY RAND()
             LIMIT :lim',
            [':cat' => $categoryId, ':exclude' => $excludeId, ':lim' => $limit]
        );
    }

    // ─── Buscador avanzado ────────────────────────────────────

    /**
     * Busca productos con filtros múltiples.
     * Retorna array vacío si los filtros son inválidos (ej. min > max).
     *
     * @param array $filters {
     *   q?: string,           texto libre
     *   categoria?: string,   slug de categoría
     *   precio_min?: float,
     *   precio_max?: float,
     *   orden?: string,       mas_recientes|mas_antiguos|precio_asc|precio_desc|con_descuento
     * }
     */
    public static function search(array $filters, int $limit = 24, int $offset = 0): array
    {
        // Validar rango de precio
        $min = isset($filters['precio_min']) ? (float)$filters['precio_min'] : null;
        $max = isset($filters['precio_max']) ? (float)$filters['precio_max'] : null;
        if ($min !== null && $max !== null && $min > $max) {
            return [];
        }

        $where  = ['p.activo = 1'];
        $params = [];

        // Texto libre (FULLTEXT)
        if (!empty($filters['q'])) {
            $where[]        = 'MATCH(p.nombre, p.descripcion) AGAINST(:q IN BOOLEAN MODE)';
            $params[':q']   = $filters['q'] . '*';
        }

        // Categoría
        if (!empty($filters['categoria'])) {
            $where[]             = 'c.slug = :cat';
            $params[':cat']      = $filters['categoria'];
        }

        // Precio mínimo
        if ($min !== null) {
            $where[]            = '(p.precio - p.descuento) >= :pmin';
            $params[':pmin']    = $min;
        }

        // Precio máximo
        if ($max !== null) {
            $where[]            = '(p.precio - p.descuento) <= :pmax';
            $params[':pmax']    = $max;
        }

        // Solo con descuento
        if (!empty($filters['con_descuento'])) {
            $where[] = 'p.descuento > 0';
        }

        // Ordenamiento
        $ordenMap = [
            'mas_recientes' => 'p.fecha_creacion DESC',
            'mas_antiguos'  => 'p.fecha_creacion ASC',
            'precio_asc'    => '(p.precio - p.descuento) ASC',
            'precio_desc'   => '(p.precio - p.descuento) DESC',
            'con_descuento' => 'p.descuento DESC, p.fecha_creacion DESC',
        ];
        $orden = $ordenMap[$filters['orden'] ?? 'mas_recientes'] ?? 'p.fecha_creacion DESC';

        $sql = 'SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
                FROM productos p
                JOIN categorias c ON c.id = p.categoria_id
                WHERE ' . implode(' AND ', $where) . "
                ORDER BY {$orden}
                LIMIT :lim OFFSET :off";

        $params[':lim']  = $limit;
        $params[':off']  = $offset;

        return static::fetchAll($sql, $params);
    }

    /** Cuenta resultados de búsqueda (para paginación). */
    public static function searchCount(array $filters): int
    {
        $min = isset($filters['precio_min']) ? (float)$filters['precio_min'] : null;
        $max = isset($filters['precio_max']) ? (float)$filters['precio_max'] : null;
        if ($min !== null && $max !== null && $min > $max) {
            return 0;
        }

        $where  = ['p.activo = 1'];
        $params = [];

        if (!empty($filters['q'])) {
            $where[]      = 'MATCH(p.nombre, p.descripcion) AGAINST(:q IN BOOLEAN MODE)';
            $params[':q'] = $filters['q'] . '*';
        }
        if (!empty($filters['categoria'])) {
            $where[]       = 'c.slug = :cat';
            $params[':cat'] = $filters['categoria'];
        }
        if ($min !== null) {
            $where[]        = '(p.precio - p.descuento) >= :pmin';
            $params[':pmin'] = $min;
        }
        if ($max !== null) {
            $where[]        = '(p.precio - p.descuento) <= :pmax';
            $params[':pmax'] = $max;
        }
        if (!empty($filters['con_descuento'])) {
            $where[] = 'p.descuento > 0';
        }

        $row = static::fetchOne(
            'SELECT COUNT(*) AS n FROM productos p
             JOIN categorias c ON c.id = p.categoria_id
             WHERE ' . implode(' AND ', $where),
            $params
        );
        return (int)($row['n'] ?? 0);
    }

    // ─── Imágenes ─────────────────────────────────────────────

    /** Retorna todas las imágenes de un producto ordenadas. */
    public static function getImages(int $productId): array
    {
        return static::fetchAll(
            'SELECT * FROM producto_imagenes WHERE producto_id = :id ORDER BY orden ASC, id ASC',
            [':id' => $productId]
        );
    }

    /** Retorna la imagen principal de un producto. */
    public static function getMainImage(int $productId): ?array
    {
        return static::fetchOne(
            'SELECT * FROM producto_imagenes WHERE producto_id = :id AND es_principal = 1 LIMIT 1',
            [':id' => $productId]
        );
    }

    /** Cuenta imágenes de un producto. */
    public static function countImages(int $productId): int
    {
        $row = static::fetchOne(
            'SELECT COUNT(*) AS n FROM producto_imagenes WHERE producto_id = :id',
            [':id' => $productId]
        );
        return (int)($row['n'] ?? 0);
    }

    /** Agrega imagen a producto. */
    public static function addImage(int $productId, string $ruta, bool $esPrincipal = false): void
    {
        // Si es principal, quitar la principal anterior
        if ($esPrincipal) {
            static::execute(
                'UPDATE producto_imagenes SET es_principal = 0 WHERE producto_id = :id',
                [':id' => $productId]
            );
        }

        $orden = static::countImages($productId) + 1;
        static::execute(
            'INSERT INTO producto_imagenes (producto_id, ruta, es_principal, orden)
             VALUES (:pid, :ruta, :principal, :orden)',
            [':pid' => $productId, ':ruta' => $ruta, ':principal' => (int)$esPrincipal, ':orden' => $orden]
        );
    }

    /** Elimina una imagen por ID. */
    public static function deleteImage(int $imageId): void
    {
        static::execute('DELETE FROM producto_imagenes WHERE id = :id', [':id' => $imageId]);
    }

    // ─── Escritura ────────────────────────────────────────────

    /** Crea un nuevo producto. Retorna el ID. */
    public static function create(array $data): int
    {
        $slug = slugify($data['nombre'], 'productos', 'slug');

        static::execute(
            'INSERT INTO productos
             (categoria_id, nombre, slug, descripcion, precio, descuento, stock, destacado, activo)
             VALUES (:cat, :nombre, :slug, :desc, :precio, :descuento, :stock, :destacado, :activo)',
            [
                ':cat'       => (int)$data['categoria_id'],
                ':nombre'    => trim($data['nombre']),
                ':slug'      => $slug,
                ':desc'      => $data['descripcion'] ?? '',
                ':precio'    => (float)($data['precio'] ?? 0),
                ':descuento' => (float)($data['descuento'] ?? 0),
                ':stock'     => (int)($data['stock'] ?? 0),
                ':destacado' => isset($data['destacado']) ? (int)$data['destacado'] : 0,
                ':activo'    => isset($data['activo']) ? (int)$data['activo'] : 1,
            ]
        );

        return (int)static::lastInsertId();
    }

    /** Actualiza un producto. */
    public static function update(int $id, array $data): void
    {
        $slug = slugify($data['nombre'], 'productos', 'slug', $id);

        static::execute(
            'UPDATE productos SET
             categoria_id = :cat, nombre = :nombre, slug = :slug,
             descripcion = :desc, precio = :precio, descuento = :descuento,
             stock = :stock, destacado = :destacado, activo = :activo
             WHERE id = :id',
            [
                ':cat'       => (int)$data['categoria_id'],
                ':nombre'    => trim($data['nombre']),
                ':slug'      => $slug,
                ':desc'      => $data['descripcion'] ?? '',
                ':precio'    => (float)($data['precio'] ?? 0),
                ':descuento' => (float)($data['descuento'] ?? 0),
                ':stock'     => (int)($data['stock'] ?? 0),
                ':destacado' => isset($data['destacado']) ? (int)$data['destacado'] : 0,
                ':activo'    => isset($data['activo']) ? (int)$data['activo'] : 1,
                ':id'        => $id,
            ]
        );
    }

    /** Alterna estado activo/inactivo. */
    public static function toggleStatus(int $id): void
    {
        static::execute(
            'UPDATE productos SET activo = IF(activo = 1, 0, 1) WHERE id = :id',
            [':id' => $id]
        );
    }

    /** Reduce el stock de un producto. Nunca deja el stock negativo. */
    public static function reduceStock(int $productId, int $qty): void
    {
        static::execute(
            'UPDATE productos SET stock = GREATEST(0, stock - :qty) WHERE id = :id',
            [':qty' => $qty, ':id' => $productId]
        );
    }
}
