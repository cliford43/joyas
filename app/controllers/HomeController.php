<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\ConfigModel;
use App\Models\ReviewModel;

/**
 * HomeController — Página principal de VILUNA.
 */
class HomeController extends Controller
{
    public function index(): void
    {
        $categorias    = CategoryModel::findActive();
        $navCategorias = $categorias;

        // Invalidar caché para datos frescos
        unset($_SESSION['config']);
        $config = ConfigModel::getAll();

        $sections = [];
        for ($i = 1; $i <= 4; $i++) {
            $activo = ($config["home_sec{$i}_activo"] ?? '1') === '1';
            if (!$activo) {
                continue;
            }

            $tipo     = $config["home_sec{$i}_tipo"] ?? '';
            $cantidad = max(1, min(24, (int)($config["home_sec{$i}_cantidad"] ?? 8)));
            $orden    = $config["home_sec{$i}_orden"] ?? 'recientes';
            $titulo   = $config["home_sec{$i}_titulo"] ?? '';
            $desc     = $config["home_sec{$i}_descripcion"] ?? '';

            // Defaults por sección si no hay config guardada
            if (empty($tipo)) {
                $defaults = [
                    1 => ['categories', 'Nuestras Colecciones', 'Encuentra la joya perfecta para cada ocasión'],
                    2 => ['featured', 'Piezas Destacadas', 'Selección especial de nuestros artesanos'],
                    3 => ['bestsellers', 'Los Más Queridos', 'Las joyas favoritas de nuestros clientes'],
                    4 => ['new', 'Recién Llegadas', 'Las últimas incorporaciones a nuestra colección'],
                ];
                $tipo = $defaults[$i][0];
                $titulo = $titulo ?: $defaults[$i][1];
                $desc = $desc ?: $defaults[$i][2];
            }

            if ($tipo === 'categories') {
                $sections[] = [
                    'tipo'        => 'categories',
                    'titulo'      => $titulo,
                    'descripcion' => $desc,
                    'categorias'  => $categorias,
                ];
                continue;
            }

            if ($tipo === 'testimonials') {
                $testimonials = ReviewModel::getTestimonials($cantidad);
                // Requirement 5.3: hide section if fewer than 3 approved testimonials
                if (count($testimonials) >= 3) {
                    $sections[] = [
                        'tipo'         => 'testimonials',
                        'titulo'       => $titulo,
                        'descripcion'  => $desc,
                        'testimonios'  => $testimonials,
                    ];
                } else {
                    // Show section with empty testimonials so the view can display a placeholder
                    $sections[] = [
                        'tipo'         => 'testimonials',
                        'titulo'       => $titulo,
                        'descripcion'  => $desc,
                        'testimonios'  => [],
                    ];
                }
                continue;
            }

            $productos = ProductModel::getBySection($tipo, $cantidad, $orden);

            foreach ($productos as &$prod) {
                $img = ProductModel::getMainImage((int)$prod['id']);
                $prod['imagen_principal'] = $img['ruta'] ?? null;
            }
            unset($prod);

            $sections[] = [
                'tipo'        => 'products',
                'titulo'      => $titulo,
                'descripcion' => $desc,
                'productos'   => $productos,
                'cards_por_fila' => $config["home_sec{$i}_cards_por_fila"] ?? '4',
            ];
        }

        $this->render('home/index', [
            'pageTitle'     => 'Joyería Fina y Exclusiva',
            'categorias'    => $categorias,
            'navCategorias' => $navCategorias,
            'sections'      => $sections,
        ]);
    }
}
