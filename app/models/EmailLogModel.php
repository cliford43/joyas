<?php

namespace App\Models;

use Core\Model;

/**
 * EmailLogModel — Bitácora de correos enviados por el sistema.
 */
class EmailLogModel extends Model
{
    /**
     * Registra un envío de correo en la bitácora.
     *
     * @param array $data ['destinatario', 'asunto', 'estado', 'error_mensaje']
     * @return int ID del registro insertado
     */
    public static function create(array $data): int
    {
        static::execute(
            'INSERT INTO correos_log (destinatario, asunto, estado, error_mensaje)
             VALUES (:destinatario, :asunto, :estado, :error_mensaje)',
            [
                ':destinatario'  => $data['destinatario'],
                ':asunto'        => $data['asunto'],
                ':estado'        => $data['estado'] ?? 'enviado',
                ':error_mensaje' => $data['error_mensaje'] ?? null,
            ]
        );

        return (int)static::lastInsertId();
    }

    /**
     * Retorna registros de la bitácora con paginación.
     *
     * @param int $limit  Cantidad de registros
     * @param int $offset Desplazamiento
     * @return array
     */
    public static function findAll(int $limit = 50, int $offset = 0): array
    {
        return static::fetchAll(
            'SELECT * FROM correos_log ORDER BY fecha_envio DESC LIMIT :limit OFFSET :offset',
            [':limit' => $limit, ':offset' => $offset]
        );
    }

    /**
     * Retorna el total de registros en la bitácora.
     */
    public static function countAll(): int
    {
        $row = static::fetchOne('SELECT COUNT(*) as total FROM correos_log');
        return (int)($row['total'] ?? 0);
    }
}
