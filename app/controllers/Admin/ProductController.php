<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use Services\FileUploader;

class ProductController extends Controller
{
    public function index(): void
    {
        $productos = ProductModel::findAll(100);
        $this->render('admin/productos/index', [
            'pageTitle'   => 'Productos',
            'currentPage' => 'productos',
            'productos'   => $productos,
        ], 'admin_layout');
    }

    public function create(): void
    {
        $categorias = CategoryModel::findAll();
        $this->render('admin/productos/form', [
            'pageTitle'   => 'Nuevo producto',
            'currentPage' => 'productos',
            'producto'    => null,
            'categorias'  => $categorias,
            'imagenes'    => [],
        ], 'admin_layout');
    }

    public function store(): void
    {
        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $this->render('admin/productos/form', [
                'pageTitle'   => 'Nuevo producto',
                'currentPage' => 'productos',
                'producto'    => null,
                'categorias'  => CategoryModel::findAll(),
                'imagenes'    => [],
                'errors'      => $errors,
                'old'         => $_POST,
            ], 'admin_layout');
            return;
        }

        $productId = ProductModel::create([
            'categoria_id' => (int)$_POST['categoria_id'],
            'nombre'       => trim($_POST['nombre']),
            'descripcion'  => trim($_POST['descripcion'] ?? ''),
            'precio'       => (float)$_POST['precio'],
            'descuento'    => (float)($_POST['descuento'] ?? 0),
            'stock'        => (int)$_POST['stock'],
            'destacado'    => isset($_POST['destacado']) ? 1 : 0,
            'activo'       => isset($_POST['activo']) ? 1 : 0,
        ]);

        $this->uploadImages($productId);

        $this->flash('success', 'Producto creado correctamente.');
        $this->redirect(url('admin/productos'));
    }

    public function edit(string $id): void
    {
        $producto = ProductModel::findById((int)$id);
        if (!$producto) { (new \App\Controllers\ErrorController())->notFound(); return; }

        $this->render('admin/productos/form', [
            'pageTitle'   => 'Editar producto',
            'currentPage' => 'productos',
            'producto'    => $producto,
            'categorias'  => CategoryModel::findAll(),
            'imagenes'    => ProductModel::getImages((int)$id),
        ], 'admin_layout');
    }

    public function update(string $id): void
    {
        $producto = ProductModel::findById((int)$id);
        if (!$producto) { (new \App\Controllers\ErrorController())->notFound(); return; }

        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $this->render('admin/productos/form', [
                'pageTitle'   => 'Editar producto',
                'currentPage' => 'productos',
                'producto'    => $producto,
                'categorias'  => CategoryModel::findAll(),
                'imagenes'    => ProductModel::getImages((int)$id),
                'errors'      => $errors,
            ], 'admin_layout');
            return;
        }

        ProductModel::update((int)$id, [
            'categoria_id' => (int)$_POST['categoria_id'],
            'nombre'       => trim($_POST['nombre']),
            'descripcion'  => trim($_POST['descripcion'] ?? ''),
            'precio'       => (float)$_POST['precio'],
            'descuento'    => (float)($_POST['descuento'] ?? 0),
            'stock'        => (int)$_POST['stock'],
            'destacado'    => isset($_POST['destacado']) ? 1 : 0,
            'activo'       => isset($_POST['activo']) ? 1 : 0,
        ]);

        $this->uploadImages((int)$id);

        $this->flash('success', 'Producto actualizado.');
        $this->redirect(url('admin/productos'));
    }

    public function toggle(string $id): void
    {
        ProductModel::toggleStatus((int)$id);
        $this->flash('success', 'Estado actualizado.');
        $this->redirect(url('admin/productos'));
    }

    public function deleteImage(string $id): void
    {
        $imageId   = (int)($_POST['image_id'] ?? 0);
        ProductModel::deleteImage($imageId);
        $this->flash('success', 'Imagen eliminada.');
        $this->redirect(url('admin/productos/' . $id . '/editar'));
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (mb_strlen(trim($data['nombre'] ?? '')) < 2) $errors[] = 'El nombre es requerido.';
        if (!isset($data['categoria_id']) || (int)$data['categoria_id'] <= 0) $errors[] = 'Selecciona una categoría.';
        if (!isset($data['precio']) || (float)$data['precio'] < 0) $errors[] = 'El precio debe ser mayor o igual a 0.';
        if (!isset($data['stock']) || (int)$data['stock'] < 0) $errors[] = 'El stock debe ser mayor o igual a 0.';
        return $errors;
    }

    private function uploadImages(int $productId): void
    {
        if (empty($_FILES['imagenes']['name'][0])) return;

        $currentCount = ProductModel::countImages($productId);
        $destDir = defined('UPLOAD_PATH') ? UPLOAD_PATH . '/productos' : ROOT_PATH . '/uploads/productos';

        $files = $_FILES['imagenes'];
        $total = count($files['name']);

        for ($i = 0; $i < $total; $i++) {
            if ($currentCount >= 10) break;
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;

            $file = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];

            try {
                $ruta = FileUploader::uploadProductImage($file, $destDir);
                ProductModel::addImage($productId, $ruta, $currentCount === 0 && $i === 0);
                $currentCount++;
            } catch (\InvalidArgumentException $e) {
                // Log silencioso — imagen inválida se omite
            }
        }
    }
}
