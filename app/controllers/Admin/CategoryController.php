<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\CategoryModel;
use Services\FileUploader;

class CategoryController extends Controller
{
    public function index(): void
    {
        $categorias = CategoryModel::findAll();
        $this->render('admin/categorias/index', [
            'pageTitle'   => 'Categorías',
            'currentPage' => 'categorias',
            'categorias'  => $categorias,
        ], 'admin_layout');
    }

    public function create(): void
    {
        $this->render('admin/categorias/form', [
            'pageTitle'   => 'Nueva categoría',
            'currentPage' => 'categorias',
            'categoria'   => null,
        ], 'admin_layout');
    }

    public function store(): void
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $errors = [];
        if (mb_strlen($nombre) < 2) $errors[] = 'El nombre debe tener al menos 2 caracteres.';

        if (!empty($errors)) {
            $this->render('admin/categorias/form', [
                'pageTitle'   => 'Nueva categoría',
                'currentPage' => 'categorias',
                'categoria'   => null,
                'errors'      => $errors,
                'old'         => $_POST,
            ], 'admin_layout');
            return;
        }

        $categoryId = CategoryModel::create([
            'nombre'      => $nombre,
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'activo'      => isset($_POST['activo']) ? 1 : 0,
        ]);

        $this->uploadImage($categoryId);

        $this->flash('success', 'Categoría creada correctamente.');
        $this->redirect(url('admin/categorias'));
    }

    public function edit(string $id): void
    {
        $categoria = CategoryModel::findById((int)$id);
        if (!$categoria) { (new \App\Controllers\ErrorController())->notFound(); return; }

        $this->render('admin/categorias/form', [
            'pageTitle'   => 'Editar categoría',
            'currentPage' => 'categorias',
            'categoria'   => $categoria,
        ], 'admin_layout');
    }

    public function update(string $id): void
    {
        $categoria = CategoryModel::findById((int)$id);
        if (!$categoria) { (new \App\Controllers\ErrorController())->notFound(); return; }

        $nombre = trim($_POST['nombre'] ?? '');
        $errors = [];
        if (mb_strlen($nombre) < 2) $errors[] = 'El nombre debe tener al menos 2 caracteres.';

        if (!empty($errors)) {
            $this->render('admin/categorias/form', [
                'pageTitle'   => 'Editar categoría',
                'currentPage' => 'categorias',
                'categoria'   => $categoria,
                'errors'      => $errors,
            ], 'admin_layout');
            return;
        }

        CategoryModel::update((int)$id, [
            'nombre'      => $nombre,
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'activo'      => isset($_POST['activo']) ? 1 : 0,
        ]);

        $this->uploadImage((int)$id, $categoria);

        $this->flash('success', 'Categoría actualizada.');
        $this->redirect(url('admin/categorias'));
    }

    public function toggle(string $id): void
    {
        CategoryModel::toggleStatus((int)$id);
        $this->flash('success', 'Estado actualizado.');
        $this->redirect(url('admin/categorias'));
    }

    private function uploadImage(int $categoryId, ?array $currentCategory = null): void
    {
        if (empty($_FILES['imagen']['name'])) {
            return;
        }

        $destDir = defined('UPLOAD_PATH') ? UPLOAD_PATH . '/categorias' : ROOT_PATH . '/uploads/categorias';

        try {
            $ruta = FileUploader::uploadProductImage($_FILES['imagen'], $destDir);
            CategoryModel::updateImage($categoryId, $ruta);

            $oldPath = str_replace('\\', '/', (string)($currentCategory['imagen'] ?? ''));
            if ($oldPath !== '' && str_starts_with($oldPath, 'uploads/categorias/')) {
                FileUploader::delete($oldPath);
            }
        } catch (\InvalidArgumentException $e) {
            $this->flash('error', $e->getMessage());
        }
    }
}
