<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\UserModel;

class UserController extends Controller
{
    public function index(): void
    {
        $usuarios = UserModel::findAll(200);
        $this->render('admin/usuarios/index', [
            'pageTitle'   => 'Usuarios',
            'currentPage' => 'usuarios',
            'usuarios'    => $usuarios,
        ], 'admin_layout');
    }

    public function show(string $id): void
    {
        $usuario = UserModel::findById((int)$id);
        if (!$usuario) { (new \App\Controllers\ErrorController())->notFound(); return; }

        $this->render('admin/usuarios/show', [
            'pageTitle'   => 'Usuario #' . $id,
            'currentPage' => 'usuarios',
            'usuario'     => $usuario,
        ], 'admin_layout');
    }

    public function toggle(string $id): void
    {
        UserModel::toggleStatus((int)$id);
        $this->flash('success', 'Estado del usuario actualizado.');
        $this->redirect(url('admin/usuarios'));
    }
}
