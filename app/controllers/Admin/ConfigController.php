<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\ConfigModel;
use Services\FileUploader;

class ConfigController extends Controller
{
    private const DEFAULT_THEME = [
        'theme_brand_primary'            => '#D4AF37',
        'theme_brand_primary_light'      => '#F5D87A',
        'theme_brand_primary_dark'       => '#B8961E',
        'theme_base_bg'                  => '#FFFFFF',
        'theme_base_text'                => '#111111',
        'theme_base_muted'               => '#6C757D',
        'theme_menu_bg'                  => '#111111',
        'theme_menu_text'                => '#FFFFFF',
        'theme_menu_hover'               => '#D4AF37',
        'theme_btn_primary_bg'           => '#D4AF37',
        'theme_btn_primary_text'         => '#111111',
        'theme_btn_primary_hover_bg'     => '#B8961E',
        'theme_btn_primary_hover_text'   => '#FFFFFF',
        'theme_btn_outline_border'       => '#D4AF37',
        'theme_btn_outline_text'         => '#D4AF37',
        'theme_btn_outline_hover_bg'     => '#D4AF37',
        'theme_btn_outline_hover_text'   => '#111111',
    ];

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
        if (!empty($_POST['reset_theme'])) {
            $oldLogo = str_replace('\\', '/', ConfigModel::get('logo_principal', ''));
            ConfigModel::setMany(self::DEFAULT_THEME);
            ConfigModel::set('logo_principal', '');

            if ($oldLogo !== '' && str_starts_with($oldLogo, 'uploads/branding/')) {
                FileUploader::delete($oldLogo);
            }

            $this->flash('success', 'Paleta y logo restablecidos a los valores por defecto.');
            $this->redirect(url('admin/configuracion'));
            return;
        }

        $allowed = [
            'nombre_tienda', 'correo_contacto', 'whatsapp', 'direccion',
            'facebook', 'instagram', 'banco_nombre', 'banco_cuenta',
            'banco_tipo', 'banco_beneficiario', 'metadescripcion',
            'slogan', 'whatsapp_mensaje',
            'hero_tagline', 'hero_titulo', 'hero_descripcion',
            'theme_brand_primary', 'theme_brand_primary_light', 'theme_brand_primary_dark',
            'theme_base_bg', 'theme_base_text', 'theme_base_muted',
            'theme_menu_bg', 'theme_menu_text', 'theme_menu_hover',
            'theme_btn_primary_bg', 'theme_btn_primary_text', 'theme_btn_primary_hover_bg', 'theme_btn_primary_hover_text',
            'theme_btn_outline_border', 'theme_btn_outline_text', 'theme_btn_outline_hover_bg', 'theme_btn_outline_hover_text',
        ];

        $data = [];
        foreach ($allowed as $key) {
            if (isset($_POST[$key])) {
                $data[$key] = trim($_POST[$key]);
            }
        }

        ConfigModel::setMany($data);

        $this->handleLogoUpload();

        $this->flash('success', 'Configuración guardada.');
        $this->redirect(url('admin/configuracion'));
    }

    private function handleLogoUpload(): void
    {
        if (empty($_FILES['logo_principal']['name'])) {
            return;
        }

        $destDir = defined('UPLOAD_PATH') ? UPLOAD_PATH . '/branding' : ROOT_PATH . '/uploads/branding';

        try {
            $newPath = FileUploader::uploadProductImage($_FILES['logo_principal'], $destDir);
            $oldPath = str_replace('\\', '/', ConfigModel::get('logo_principal', ''));

            ConfigModel::set('logo_principal', $newPath);

            if ($oldPath !== '' && str_starts_with($oldPath, 'uploads/branding/')) {
                FileUploader::delete($oldPath);
            }
        } catch (\InvalidArgumentException $e) {
            $this->flash('error', 'Logo no actualizado: ' . $e->getMessage());
        }
    }
}
