<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\CouponModel;

class CouponController extends Controller
{
    public function index(): void
    {
        $cupones = CouponModel::findAll();
        $this->render('admin/cupones/index', [
            'pageTitle'   => 'Cupones',
            'currentPage' => 'cupones',
            'cupones'     => $cupones,
        ], 'admin_layout');
    }

    public function create(): void
    {
        $this->render('admin/cupones/form', [
            'pageTitle'   => 'Nuevo cupón',
            'currentPage' => 'cupones',
            'cupon'       => null,
        ], 'admin_layout');
    }

    public function store(): void
    {
        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $this->render('admin/cupones/form', [
                'pageTitle'   => 'Nuevo cupón',
                'currentPage' => 'cupones',
                'cupon'       => null,
                'errors'      => $errors,
                'old'         => $_POST,
            ], 'admin_layout');
            return;
        }
        CouponModel::create($_POST);
        $this->flash('success', 'Cupón creado.');
        $this->redirect(url('admin/cupones'));
    }

    public function edit(string $id): void
    {
        $cupon = CouponModel::findById((int)$id);
        if (!$cupon) { (new \App\Controllers\ErrorController())->notFound(); return; }
        $this->render('admin/cupones/form', [
            'pageTitle'   => 'Editar cupón',
            'currentPage' => 'cupones',
            'cupon'       => $cupon,
        ], 'admin_layout');
    }

    public function update(string $id): void
    {
        $cupon = CouponModel::findById((int)$id);
        if (!$cupon) { (new \App\Controllers\ErrorController())->notFound(); return; }

        $errors = $this->validate($_POST);
        if (!empty($errors)) {
            $this->render('admin/cupones/form', [
                'pageTitle'   => 'Editar cupón',
                'currentPage' => 'cupones',
                'cupon'       => $cupon,
                'errors'      => $errors,
            ], 'admin_layout');
            return;
        }
        CouponModel::update((int)$id, $_POST);
        $this->flash('success', 'Cupón actualizado.');
        $this->redirect(url('admin/cupones'));
    }

    public function toggle(string $id): void
    {
        CouponModel::toggleStatus((int)$id);
        $this->flash('success', 'Estado actualizado.');
        $this->redirect(url('admin/cupones'));
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (mb_strlen(trim($data['codigo'] ?? '')) < 2) $errors[] = 'El código es requerido.';
        if (!isset($data['porcentaje']) || (float)$data['porcentaje'] <= 0 || (float)$data['porcentaje'] > 100) $errors[] = 'El porcentaje debe estar entre 1 y 100.';
        if (empty($data['fecha_expiracion'])) $errors[] = 'La fecha de expiración es requerida.';
        return $errors;
    }
}
