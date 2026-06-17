<?php

namespace Services;

/**
 * Services\FileUploader — Servicio seguro de subida de archivos.
 * Valida extensión, tipo MIME real (finfo_file), tamaño y genera nombre único.
 */
class FileUploader
{
    // MIME types permitidos por contexto
    private const ALLOWED_IMAGE_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
    ];

    private const ALLOWED_VOUCHER_MIMES = [
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
        'application/pdf' => 'pdf',
    ];

    private const MAX_IMAGE_SIZE   = 2 * 1024 * 1024;  // 2 MB
    private const MAX_VOUCHER_SIZE = 5 * 1024 * 1024;  // 5 MB

    /**
     * Sube una imagen de producto.
     *
     * @param array  $file      Elemento de $_FILES
     * @param string $destDir   Directorio de destino (absoluto)
     * @return string           Ruta relativa guardada (desde /uploads/...)
     * @throws \InvalidArgumentException si el archivo no es válido
     */
    public static function uploadProductImage(array $file, string $destDir): string
    {
        return static::upload($file, $destDir, self::ALLOWED_IMAGE_MIMES, self::MAX_IMAGE_SIZE);
    }

    /**
     * Sube un comprobante de transferencia bancaria (JPG, PNG, PDF).
     *
     * @param array  $file      Elemento de $_FILES
     * @param string $destDir   Directorio de destino (absoluto)
     * @return string           Ruta relativa guardada
     * @throws \InvalidArgumentException si el archivo no es válido
     */
    public static function uploadVoucher(array $file, string $destDir): string
    {
        return static::upload($file, $destDir, self::ALLOWED_VOUCHER_MIMES, self::MAX_VOUCHER_SIZE);
    }

    /**
     * Método genérico de subida con validación completa.
     */
    private static function upload(array $file, string $destDir, array $allowedMimes, int $maxSize): string
    {
        // 1. Verificar que se subió correctamente
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException(
                static::uploadErrorMessage($file['error'] ?? UPLOAD_ERR_NO_FILE)
            );
        }

        // 2. Validar tamaño
        if ($file['size'] > $maxSize) {
            $maxMb = number_format($maxSize / 1024 / 1024, 0);
            throw new \InvalidArgumentException(
                "El archivo excede el tamaño máximo permitido de {$maxMb} MB."
            );
        }

        // 3. Validar tipo MIME real usando finfo (no la extensión declarada por el cliente)
        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file['tmp_name']);

        if (!array_key_exists($realMime, $allowedMimes)) {
            $allowed = implode(', ', array_keys($allowedMimes));
            throw new \InvalidArgumentException(
                "Tipo de archivo no permitido ({$realMime}). Se aceptan: {$allowed}."
            );
        }

        // 4. Crear directorio si no existe
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        // 5. Generar nombre único con extensión real según MIME
        $ext      = $allowedMimes[$realMime];
        $filename = uniqid('viluna_', true) . '_' . time() . '.' . $ext;
        $destPath = rtrim($destDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        // 6. Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            throw new \InvalidArgumentException('Error al guardar el archivo. Intenta de nuevo.');
        }

        // 7. Retornar ruta relativa desde la raíz del proyecto
        $rootPath = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__);
        $relativePath = str_replace($rootPath, '', $destPath);
        $relativePath = str_replace('\\', '/', $relativePath);
        return ltrim($relativePath, '/');
    }

    /**
     * Elimina un archivo subido (limpieza al eliminar producto o rechazar comprobante).
     */
    public static function delete(string $relativePath): void
    {
        $rootPath = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__);
        $fullPath = $rootPath . DIRECTORY_SEPARATOR . ltrim($relativePath, '/\\');
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    private static function uploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'El archivo supera el tamaño máximo permitido.',
            UPLOAD_ERR_PARTIAL  => 'El archivo se subió parcialmente. Intenta de nuevo.',
            UPLOAD_ERR_NO_FILE  => 'No se seleccionó ningún archivo.',
            default             => 'Error al subir el archivo (código ' . $code . ').',
        };
    }
}
