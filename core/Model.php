<?php

namespace Core;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Core\Model — Clase base para todos los modelos
 * Provee una instancia PDO compartida (Singleton) y métodos de consulta preparada.
 */
abstract class Model
{
    private static ?PDO $pdo = null;

    /**
     * Obtiene (o crea) la instancia PDO compartida.
     */
    protected static function getDb(): PDO
    {
        if (self::$pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_PORT,
                DB_NAME,
                DB_CHARSET
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_FOUND_ROWS   => true,
            ];

            try {
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // En producción no revelar detalles
                if (defined('APP_ENV') && APP_ENV === 'development') {
                    throw $e;
                }
                error_log('DB Connection failed: ' . $e->getMessage());
                die('Error de conexión a la base de datos.');
            }
        }

        return self::$pdo;
    }

    /**
     * Permite inyectar una instancia PDO (útil para pruebas).
     */
    public static function setDb(PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    /**
     * Expone la instancia PDO para helpers externos.
     */
    public static function getDbPublic(): PDO
    {
        return self::getDb();
    }

    /**
     * Ejecuta una consulta preparada y retorna el PDOStatement.
     */
    protected static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Retorna todas las filas de una consulta.
     */
    protected static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    /**
     * Retorna la primera fila de una consulta, o null.
     */
    protected static function fetchOne(string $sql, array $params = []): ?array
    {
        $row = self::query($sql, $params)->fetch();
        return $row !== false ? $row : null;
    }

    /**
     * Ejecuta una consulta de escritura (INSERT/UPDATE/DELETE) y retorna el número de filas afectadas.
     */
    protected static function execute(string $sql, array $params = []): int
    {
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Retorna el último ID insertado.
     */
    protected static function lastInsertId(): string
    {
        return self::getDb()->lastInsertId();
    }

    /**
     * Inicia una transacción.
     */
    protected static function beginTransaction(): void
    {
        self::getDb()->beginTransaction();
    }

    /**
     * Confirma una transacción.
     */
    protected static function commit(): void
    {
        self::getDb()->commit();
    }

    /**
     * Revierte una transacción.
     */
    protected static function rollback(): void
    {
        self::getDb()->rollBack();
    }
}
