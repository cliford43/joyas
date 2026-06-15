<?php

namespace App\Models;

use Core\Model;

/**
 * CategoryModel — Gestión de categorías de productos VILUNA.
 */
class CategoryModel extends Model
{
    /** Retorna todas las categorías. */
    public static function findAll(): array
    {
        return static::fetchAll(
            'SELECT * FROM categorias ORDER BY nombre ASC'
        );
    }

    /** Retorna solo categorías activas. */
    public static function findActive(): array
    {
        return static::fetchAll(
            'SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre ASC'
        );
    }

    /** Busca categoría por slug. */
    public static function findBySlug(string $slug): ?array
    {
        return static::fetchOne(
            'SELECT * FROM categorias WHERE slug = :slug LIMIT 1',
            [':slug' => $slug]
        );
    }

    /** Busca categoría por ID. */
    public static function findById(int $id): ?array
    {
        return static::fetchOne(
            'SELECT * FROM categorias WHERE id = :id LIMIT 1',
            [':id' => $id]
        );
    }

    /** Cuenta total de categorías. */
    public static function count(): int
    {
        $row = static::fetchOne('SELECT COUNT(*) AS n FROM categorias');
        return (int)($row['n'] ?? 0);
    }

    /** Crea una nueva categoría. Retorna el ID. */
    public static function create(array $data): int
    {
        $slug = slugify($data['nombre'], 'categorias', 'slug');

        static::execute(
            'INSERT INTO categorias (nombre, slug, descripcion, activo)
             VALUES (:nombre, :slug, :descripcion, :activo)',
            [
                ':nombre'      => trim($data['nombre']),
                ':slug'        => $slug,
                ':descripcion' => $data['descripcion'] ?? null,
                ':activo'      => isset($data['activo']) ? (int)$data['activo'] : 1,
            ]
        );

        return (int)static::lastInsertId();
    }

    /** Actualiza una categoría. */
    public static function update(int $id, array $data): void
    {
        $slug = slugify($data['nombre'], 'categorias', 'slug', $id);

        static::execute(
            'UPDATE categorias SET nombre = :nombre, slug = :slug,
             descripcion = :descripcion, activo = :activo WHERE id = :id',
            [
                ':nombre'      => trim($data['nombre']),
                ':slug'        => $slug,
                ':descripcion' => $data['descripcion'] ?? null,
                ':activo'      => isset($data['activo']) ? (int)$data['activo'] : 1,
                ':id'          => $id,
            ]
        );
    }

    /** Alterna estado activo/inactivo. */
    public static function toggleStatus(int $id): void
    {
        static::execute(
            'UPDATE categorias SET activo = IF(activo = 1, 0, 1) WHERE id = :id',
            [':id' => $id]
        );
    }

    /** Actualiza la imagen de la categoría. */
    public static function updateImage(int $id, string $ruta): void
    {
        static::execute(
            'UPDATE categorias SET imagen = :ruta WHERE id = :id',
            [':ruta' => $ruta, ':id' => $id]
        );
    }
}
