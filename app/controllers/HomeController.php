<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\ConfigModel;

/**
 * HomeController — Página principal de VILUNA.
 */
class HomeController extends Controller
{
    public function index(): void
    {
        $categorias    = CategoryModel::findActive();
        $masVendidos   = ProductModel::getBestsellers(8);
        $nuevos        = ProductModel::getNew(8);
        $destacados    = ProductModel::getFeatured(8);
        $navCategorias = $categorias;

        // Cargar imágenes principales para cada sección
        foreach (['masVendidos', 'nuevos', 'destacados'] as $var) {
            foreach ($$var as &$prod) {
                $img = ProductModel::getMainImage((int)$prod['id']);
                $prod['imagen_principal'] = $img['ruta'] ?? null;
            }
            unset($prod);
        }

        $this->render('home/index', [
            'pageTitle'     => 'Joyería Fina y Exclusiva',
            'categorias'    => $categorias,
            'masVendidos'   => $masVendidos,
            'nuevos'        => $nuevos,
            'destacados'    => $destacados,
            'navCategorias' => $navCategorias,
        ]);
    }
}
