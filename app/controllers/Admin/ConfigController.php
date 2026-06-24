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
            $oldHero = str_replace('\\', '/', ConfigModel::get('hero_imagen', ''));
            ConfigModel::setMany(self::DEFAULT_THEME);
            ConfigModel::set('logo_principal', '');
            ConfigModel::set('hero_imagen', '');

            if ($oldLogo !== '' && str_starts_with($oldLogo, 'uploads/branding/')) {
                FileUploader::delete($oldLogo);
            }
            if ($oldHero !== '' && str_starts_with($oldHero, 'uploads/hero/')) {
                FileUploader::delete($oldHero);
            }

            $this->flash('success', 'Paleta, logo e imagen hero restablecidos a los valores por defecto.');
            $this->redirect(url('admin/configuracion'));
            return;
        }

        $allowed = [
            'nombre_tienda', 'correo_contacto', 'whatsapp', 'direccion',
            'facebook', 'instagram', 'banco_nombre', 'banco_cuenta',
            'banco_tipo', 'banco_beneficiario', 'metadescripcion',
            'slogan', 'whatsapp_mensaje',
            'hero_tagline', 'hero_titulo', 'hero_descripcion', 'hero_fondo_color', 'hero_activo',
            'cards_por_fila', 'cards_por_fila_catalogo', 'cards_por_fila_busqueda', 'cards_por_fila_wishlist',
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

        // Checkbox hero_activo: si no viene en POST, es '0'
        $data['hero_activo'] = isset($_POST['hero_activo']) ? '1' : '0';

        // Validar cards_por_fila: solo permitir valores válidos
        $validCardsPerRow = ['2', '3', '4', '6'];
        $cardsKeys = ['cards_por_fila', 'cards_por_fila_catalogo', 'cards_por_fila_busqueda', 'cards_por_fila_wishlist'];
        foreach ($cardsKeys as $ck) {
            if (isset($data[$ck]) && !in_array($data[$ck], $validCardsPerRow, true)) {
                unset($data[$ck]); // discard invalid value
            }
        }

        ConfigModel::setMany($data);

        $this->handleLogoUpload();
        $this->handleHeroImageUpload();

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

    private function handleHeroImageUpload(): void
    {
        if (empty($_FILES['hero_imagen']['name'])) {
            return;
        }

        $destDir = defined('UPLOAD_PATH') ? UPLOAD_PATH . '/hero' : ROOT_PATH . '/uploads/hero';

        try {
            $newPath = FileUploader::uploadProductImage($_FILES['hero_imagen'], $destDir);
            $oldPath = str_replace('\\', '/', ConfigModel::get('hero_imagen', ''));

            ConfigModel::set('hero_imagen', $newPath);

            if ($oldPath !== '' && str_starts_with($oldPath, 'uploads/hero/')) {
                FileUploader::delete($oldPath);
            }
        } catch (\InvalidArgumentException $e) {
            $this->flash('error', 'Imagen hero no actualizada: ' . $e->getMessage());
        }
    }

    // ─── Secciones del Home ──────────────────────────────────

    public const HOME_SECTION_TYPES = [
        'bestsellers'     => 'Productos más vendidos',
        'new'             => 'Productos nuevos / recién agregados',
        'featured'        => 'Productos destacados manualmente',
        'on_sale'         => 'Productos en oferta',
        'most_wishlisted' => 'Productos más agregados a favoritos',
        'limited_stock'   => 'Productos con inventario limitado',
        'most_viewed'     => 'Productos más vistos',
        'trending_month'  => 'Productos tendencia del mes',
        'categories'      => 'Categorías destacadas',
        'top_rated'       => 'Productos mejor valorados',
        'most_reviewed'   => 'Productos con más comentarios',
        'testimonials'    => 'Testimonios destacados',
    ];

    public function homeSections(): void
    {
        $config = ConfigModel::getAll();
        $sections = [];
        for ($i = 1; $i <= 4; $i++) {
            $sections[$i] = [
                'titulo'        => $config["home_sec{$i}_titulo"] ?? '',
                'descripcion'   => $config["home_sec{$i}_descripcion"] ?? '',
                'tipo'          => $config["home_sec{$i}_tipo"] ?? '',
                'cantidad'      => $config["home_sec{$i}_cantidad"] ?? '8',
                'activo'        => $config["home_sec{$i}_activo"] ?? '1',
                'orden'         => $config["home_sec{$i}_orden"] ?? 'recientes',
                'cards_por_fila'=> $config["home_sec{$i}_cards_por_fila"] ?? '4',
            ];
        }

        $this->render('admin/configuracion/home_sections', [
            'pageTitle'   => 'Secciones de la página principal',
            'currentPage' => 'configuracion',
            'sections'    => $sections,
            'tipos'       => self::HOME_SECTION_TYPES,
        ], 'admin_layout');
    }

    public function updateHomeSections(): void
    {
        // Restablecer secciones a valores por defecto
        if (!empty($_POST['reset_sections'])) {
            $defaults = [
                1 => ['Nuestras Colecciones', 'Encuentra la joya perfecta para cada ocasión', 'categories', '8', '1', 'recientes'],
                2 => ['Piezas Destacadas', 'Selección especial de nuestros artesanos', 'featured', '8', '1', 'recientes'],
                3 => ['Los Más Queridos', 'Las joyas favoritas de nuestros clientes', 'bestsellers', '8', '1', 'recientes'],
                4 => ['Recién Llegadas', 'Las últimas incorporaciones a nuestra colección', 'new', '8', '1', 'recientes'],
            ];

            for ($i = 1; $i <= 4; $i++) {
                ConfigModel::set("home_sec{$i}_titulo", $defaults[$i][0]);
                ConfigModel::set("home_sec{$i}_descripcion", $defaults[$i][1]);
                ConfigModel::set("home_sec{$i}_tipo", $defaults[$i][2]);
                ConfigModel::set("home_sec{$i}_cantidad", $defaults[$i][3]);
                ConfigModel::set("home_sec{$i}_activo", $defaults[$i][4]);
                ConfigModel::set("home_sec{$i}_orden", $defaults[$i][5]);
                ConfigModel::set("home_sec{$i}_cards_por_fila", '4');
            }

            $this->flash('success', 'Secciones restablecidas a los valores por defecto.');
            $this->redirect(url('admin/configuracion/home'));
            return;
        }

        for ($i = 1; $i <= 4; $i++) {
            $prefix = "sec{$i}_";
            ConfigModel::set("home_sec{$i}_titulo", trim($_POST[$prefix . 'titulo'] ?? ''));
            ConfigModel::set("home_sec{$i}_descripcion", trim($_POST[$prefix . 'descripcion'] ?? ''));
            ConfigModel::set("home_sec{$i}_tipo", trim($_POST[$prefix . 'tipo'] ?? 'bestsellers'));
            ConfigModel::set("home_sec{$i}_cantidad", max(1, min(24, (int)($_POST[$prefix . 'cantidad'] ?? 8))));
            ConfigModel::set("home_sec{$i}_activo", isset($_POST[$prefix . 'activo']) ? '1' : '0');
            ConfigModel::set("home_sec{$i}_orden", trim($_POST[$prefix . 'orden'] ?? 'recientes'));
            // Validar y guardar cards_por_fila por sección
            $cardsVal = trim($_POST[$prefix . 'cards_por_fila'] ?? '4');
            if (!in_array($cardsVal, ['2', '3', '4', '6'], true)) {
                $cardsVal = '4';
            }
            ConfigModel::set("home_sec{$i}_cards_por_fila", $cardsVal);
        }

        $this->flash('success', 'Secciones de la página principal actualizadas.');
        $this->redirect(url('admin/configuracion/home'));
    }
}
