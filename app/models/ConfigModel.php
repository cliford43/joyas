<?php

namespace App\Models;

use Core\Model;

/**
 * ConfigModel — Configuración general de la tienda.
 * Carga todos los valores en $_SESSION['config'] para evitar múltiples consultas por petición.
 */
class ConfigModel extends Model
{
    private const SESSION_KEY = 'config';

    /**
     * Retorna todos los valores de configuración como array clave => valor.
     * Usa caché en sesión — solo consulta la DB una vez por petición.
     */
    public static function getAll(): array
    {
        if (isset($_SESSION[self::SESSION_KEY]) && is_array($_SESSION[self::SESSION_KEY])) {
            return $_SESSION[self::SESSION_KEY];
        }

        $rows   = static::fetchAll('SELECT clave, valor FROM configuracion');
        $config = [];
        foreach ($rows as $row) {
            $config[$row['clave']] = $row['valor'];
        }

        $_SESSION[self::SESSION_KEY] = $config;
        return $config;
    }

    /** Retorna el valor de una clave específica. */
    public static function get(string $key, string $default = ''): string
    {
        $config = static::getAll();
        return $config[$key] ?? $default;
    }

    /** Actualiza o inserta el valor de una clave. */
    public static function set(string $key, string $value): void
    {
        static::execute(
            'INSERT INTO configuracion (clave, valor) VALUES (:key, :val)
             ON DUPLICATE KEY UPDATE valor = :val2',
            [':key' => $key, ':val' => $value, ':val2' => $value]
        );

        // Invalidar caché de sesión para reflejar cambios
        unset($_SESSION[self::SESSION_KEY]);
    }

    /**
     * Guarda múltiples valores de configuración de una vez.
     *
     * @param array $data clave => valor
     */
    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::set((string)$key, (string)$value);
        }
    }
}
