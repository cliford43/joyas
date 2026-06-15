<?php

namespace App\Models;

use Core\Model;

/**
 * UserModel — Gestión de usuarios VILUNA.
 */
class UserModel extends Model
{
    protected static string $table = 'usuarios';

    // ─── Consultas ────────────────────────────────────────────

    /** Busca usuario por correo electrónico. */
    public static function findByEmail(string $email): ?array
    {
        return static::fetchOne(
            'SELECT * FROM usuarios WHERE correo = :email LIMIT 1',
            [':email' => strtolower(trim($email))]
        );
    }

    /** Busca usuario por ID. */
    public static function findById(int $id): ?array
    {
        return static::fetchOne(
            'SELECT * FROM usuarios WHERE id = :id LIMIT 1',
            [':id' => $id]
        );
    }

    /** Busca usuario por Google ID. */
    public static function findByGoogleId(string $googleId): ?array
    {
        return static::fetchOne(
            'SELECT * FROM usuarios WHERE google_id = :gid LIMIT 1',
            [':gid' => $googleId]
        );
    }

    /** Retorna todos los usuarios (para panel admin). */
    public static function findAll(int $limit = 100, int $offset = 0): array
    {
        return static::fetchAll(
            'SELECT id, nombre, apellido, correo, rol, verificado, fecha_creacion
             FROM usuarios ORDER BY fecha_creacion DESC LIMIT :lim OFFSET :off',
            [':lim' => $limit, ':off' => $offset]
        );
    }

    /** Cuenta total de usuarios registrados. */
    public static function count(): int
    {
        $row = static::fetchOne('SELECT COUNT(*) AS total FROM usuarios');
        return (int)($row['total'] ?? 0);
    }

    // ─── Escritura ────────────────────────────────────────────

    /**
     * Crea un nuevo usuario con contraseña hasheada.
     * Retorna el ID insertado.
     */
    public static function create(array $data): int
    {
        $hash = isset($data['password'])
            ? password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12])
            : null;

        $code = static::generateVerificationCode();

        static::execute(
            'INSERT INTO usuarios
             (nombre, apellido, correo, password, telefono, direccion, rol, verificado, codigo_verificacion)
             VALUES (:nombre, :apellido, :correo, :password, :telefono, :direccion, :rol, 0, :codigo)',
            [
                ':nombre'   => trim($data['nombre']),
                ':apellido' => trim($data['apellido']),
                ':correo'   => strtolower(trim($data['correo'])),
                ':password' => $hash,
                ':telefono' => $data['telefono'] ?? null,
                ':direccion'=> $data['direccion'] ?? null,
                ':rol'      => $data['rol'] ?? 'cliente',
                ':codigo'   => $code,
            ]
        );

        return (int)static::lastInsertId();
    }

    /**
     * Crea usuario desde Google OAuth (ya verificado, sin contraseña).
     */
    public static function createFromGoogle(array $data): int
    {
        static::execute(
            'INSERT INTO usuarios (nombre, apellido, correo, google_id, rol, verificado)
             VALUES (:nombre, :apellido, :correo, :gid, "cliente", 1)',
            [
                ':nombre'   => trim($data['nombre']),
                ':apellido' => trim($data['apellido'] ?? ''),
                ':correo'   => strtolower(trim($data['correo'])),
                ':gid'      => $data['google_id'],
            ]
        );

        return (int)static::lastInsertId();
    }

    /**
     * Verifica la cuenta usando el código de verificación.
     * Retorna true si el código coincide y la cuenta fue activada.
     */
    public static function verify(int $userId, string $code): bool
    {
        $user = static::findById($userId);
        if (!$user || (int)$user['verificado'] === 1) {
            return false;
        }
        if ($user['codigo_verificacion'] !== $code) {
            return false;
        }

        static::execute(
            'UPDATE usuarios SET verificado = 1, codigo_verificacion = NULL WHERE id = :id',
            [':id' => $userId]
        );
        return true;
    }

    /**
     * Verifica la cuenta usando correo + código (sin requerir ID).
     */
    public static function verifyByEmail(string $email, string $code): bool
    {
        $user = static::findByEmail($email);
        if (!$user || (int)$user['verificado'] === 1) {
            return false;
        }
        if ($user['codigo_verificacion'] !== $code) {
            return false;
        }

        static::execute(
            'UPDATE usuarios SET verificado = 1, codigo_verificacion = NULL WHERE id = :id',
            [':id' => $user['id']]
        );
        return true;
    }

    /**
     * Regenera y guarda un nuevo código de verificación.
     * Retorna el código generado.
     */
    public static function regenerateVerificationCode(int $userId): string
    {
        $code = static::generateVerificationCode();
        static::execute(
            'UPDATE usuarios SET codigo_verificacion = :code WHERE id = :id',
            [':code' => $code, ':id' => $userId]
        );
        return $code;
    }

    /**
     * Actualiza el perfil del usuario.
     */
    public static function updateProfile(int $userId, array $data): void
    {
        static::execute(
            'UPDATE usuarios SET nombre = :nombre, apellido = :apellido,
             telefono = :telefono, direccion = :direccion WHERE id = :id',
            [
                ':nombre'    => trim($data['nombre']),
                ':apellido'  => trim($data['apellido']),
                ':telefono'  => $data['telefono'] ?? null,
                ':direccion' => $data['direccion'] ?? null,
                ':id'        => $userId,
            ]
        );
    }

    /**
     * Cambia la contraseña del usuario.
     * Verifica la contraseña actual antes de actualizarla.
     * Retorna true si se actualizó correctamente.
     */
    public static function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = static::findById($userId);
        if (!$user || empty($user['password'])) {
            return false;
        }
        if (!password_verify($currentPassword, $user['password'])) {
            return false;
        }

        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        static::execute(
            'UPDATE usuarios SET password = :hash WHERE id = :id',
            [':hash' => $hash, ':id' => $userId]
        );
        return true;
    }

    /**
     * Fuerza el cambio de contraseña (sin verificar la actual — para reset).
     */
    public static function forceChangePassword(int $userId, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        static::execute(
            'UPDATE usuarios SET password = :hash WHERE id = :id',
            [':hash' => $hash, ':id' => $userId]
        );
    }

    /**
     * Genera y guarda un token de restablecimiento de contraseña.
     * Retorna el token generado.
     */
    public static function setResetToken(int $userId): string
    {
        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hora

        static::execute(
            'UPDATE usuarios SET reset_token = :token, reset_token_expires = :expires WHERE id = :id',
            [':token' => $token, ':expires' => $expires, ':id' => $userId]
        );
        return $token;
    }

    /**
     * Busca un usuario por su token de reset válido (no expirado).
     */
    public static function findByResetToken(string $token): ?array
    {
        return static::fetchOne(
            'SELECT * FROM usuarios
             WHERE reset_token = :token
               AND reset_token_expires > NOW()
             LIMIT 1',
            [':token' => $token]
        );
    }

    /**
     * Invalida el token de reset tras su uso.
     */
    public static function clearResetToken(int $userId): void
    {
        static::execute(
            'UPDATE usuarios SET reset_token = NULL, reset_token_expires = NULL WHERE id = :id',
            [':id' => $userId]
        );
    }

    /**
     * Activa o desactiva la cuenta de un usuario.
     * Usa el campo verificado: 1 = activo, 0 = inactivo.
     */
    public static function toggleStatus(int $userId): void
    {
        static::execute(
            'UPDATE usuarios SET verificado = IF(verificado = 1, 0, 1) WHERE id = :id',
            [':id' => $userId]
        );
    }

    // ─── Helpers ──────────────────────────────────────────────

    /**
     * Genera un código de verificación de 6 dígitos numéricos.
     */
    public static function generateVerificationCode(): string
    {
        return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verifica si un correo ya está registrado.
     */
    public static function emailExists(string $email): bool
    {
        $row = static::fetchOne(
            'SELECT COUNT(*) AS n FROM usuarios WHERE correo = :email',
            [':email' => strtolower(trim($email))]
        );
        return (int)($row['n'] ?? 0) > 0;
    }
}
