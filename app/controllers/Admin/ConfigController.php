<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\ConfigModel;

class ConfigController extends Controller
{
    public function index(): void
    {
        $config = ConfigModel::getAll();
        $this->render('admin/configuracion/index', [
            'pageTitle'   => 'Configuración',
            'currentPage' => 'configuracion',
            'config'      => $config,
        ], 'admin_layout');
    }

    public function update(): void
    {
        $allowed = [
            'nombre_tienda', 'correo_contacto', 'whatsapp', 'direccion',
            'facebook', 'instagram', 'banco_nombre', 'banco_cuenta',
            'banco_tipo', 'banco_beneficiario', 'metadescripcion',
            'slogan', 'whatsapp_mensaje',
        ];

        $data = [];
        foreach ($allowed as $key) {
            if (isset($_POST[$key])) {
                $data[$key] = trim($_POST[$key]);
            }
        }

        ConfigModel::setMany($data);

        $this->flash('success', 'Configuración guardada.');
        $this->redirect(url('admin/configuracion'));
    }
}
