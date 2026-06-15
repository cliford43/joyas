<?php

namespace App\Models;

use Core\Model;

/**
 * NewsletterModel — Suscripciones al newsletter.
 */
class NewsletterModel extends Model
{
    /**
     * Suscribe un correo al newsletter.
     * Retorna true si fue suscrito, false si ya existía.
     */
    public static function subscribe(string $email): bool
    {
        $email = strtolower(trim($email));

        if (static::exists($email)) {
            return false;
        }

        static::execute(
            'INSERT INTO newsletter (correo) VALUES (:correo)',
            [':correo' => $email]
        );
        return true;
    }

    /** Verifica si un correo ya está suscrito. */
    public static function exists(string $email): bool
    {
        $row = static::fetchOne(
            'SELECT COUNT(*) AS n FROM newsletter WHERE correo = :correo',
            [':correo' => strtolower(trim($email))]
        );
        return (int)($row['n'] ?? 0) > 0;
    }

    /** Retorna todos los suscriptores. */
    public static function findAll(): array
    {
        return static::fetchAll(
            'SELECT * FROM newsletter ORDER BY fecha_suscripcion DESC'
        );
    }

    /** Cuenta total de suscriptores. */
    public static function count(): int
    {
        $row = static::fetchOne('SELECT COUNT(*) AS n FROM newsletter');
        return (int)($row['n'] ?? 0);
    }
}
