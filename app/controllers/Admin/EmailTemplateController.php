<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\EmailTemplateModel;

class EmailTemplateController extends Controller
{
    /**
     * Lista todas las plantillas de correo.
     */
    public function index(): void
    {
        $plantillas = EmailTemplateModel::findAll();

        $this->render('admin/plantillas/index', [
            'pageTitle'   => 'Plantillas de Correo',
            'currentPage' => 'plantillas',
            'plantillas'  => $plantillas,
        ], 'admin_layout');
    }

    /**
     * Formulario de edición de una plantilla.
     */
    public function edit(string $id): void
    {
        $plantilla = EmailTemplateModel::findById((int)$id);

        if (!$plantilla) {
            $this->flash('error', 'Plantilla no encontrada.');
            $this->redirect(url('admin/plantillas-correo'));
            return;
        }

        $variables = json_decode($plantilla['variables'] ?? '[]', true) ?: [];

        $this->render('admin/plantillas/edit', [
            'pageTitle'   => 'Editar Plantilla',
            'currentPage' => 'plantillas',
            'plantilla'   => $plantilla,
            'variables'   => $variables,
        ], 'admin_layout');
    }

    /**
     * Guarda cambios en una plantilla.
     */
    public function update(string $id): void
    {
        $plantilla = EmailTemplateModel::findById((int)$id);

        if (!$plantilla) {
            $this->flash('error', 'Plantilla no encontrada.');
            $this->redirect(url('admin/plantillas-correo'));
            return;
        }

        $asunto    = trim($_POST['asunto'] ?? '');
        $contenido = trim($_POST['contenido'] ?? '');

        $errors = [];
        if ($asunto === '') {
            $errors[] = 'El asunto es requerido.';
        }
        if ($contenido === '') {
            $errors[] = 'El contenido es requerido.';
        }

        if (!empty($errors)) {
            $variables = json_decode($plantilla['variables'] ?? '[]', true) ?: [];
            $this->render('admin/plantillas/edit', [
                'pageTitle'   => 'Editar Plantilla',
                'currentPage' => 'plantillas',
                'plantilla'   => array_merge($plantilla, ['asunto' => $asunto, 'contenido' => $contenido]),
                'variables'   => $variables,
                'errors'      => $errors,
            ], 'admin_layout');
            return;
        }

        EmailTemplateModel::update((int)$id, [
            'asunto'    => $asunto,
            'contenido' => $contenido,
        ]);

        $this->flash('success', 'Plantilla actualizada correctamente.');
        $this->redirect(url('admin/plantillas-correo'));
    }
}
