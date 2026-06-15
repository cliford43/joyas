<?php

namespace App\Controllers;

use Core\Controller;

class UploadController extends Controller
{
    public function brandingAsset(string $filename): void
    {
        $this->serveUpload('branding', $filename, false);
    }

    public function categoryImage(string $filename): void
    {
        $this->serveUpload('categorias', $filename, false);
    }

    public function productImage(string $filename): void
    {
        $this->serveUpload('productos', $filename, false);
    }

    public function voucher(string $filename): void
    {
        $this->serveUpload('comprobantes', $filename, true);
    }

    private function serveUpload(string $folder, string $filename, bool $authRequired): void
    {
        if ($authRequired && !$this->isAuthenticated()) {
            http_response_code(403);
            echo 'Acceso denegado';
            return;
        }

        $safeFilename = basename(str_replace('\\', '/', $filename));
        $basePath = defined('UPLOAD_PATH') ? UPLOAD_PATH : ROOT_PATH . '/uploads';
        $filePath = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $safeFilename;

        if (!is_file($filePath)) {
            http_response_code(404);
            echo 'Archivo no encontrado';
            return;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($filePath) ?: 'application/octet-stream';

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . (string)filesize($filePath));
        header('Cache-Control: public, max-age=86400');
        readfile($filePath);
        exit;
    }
}