<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\ProductModel;
use App\Models\CategoryModel;

/**
 * SearchController — Buscador avanzado con soporte AJAX.
 */
class SearchController extends Controller
{
    public function handle(): void
    {
        $filters = [
            'q'           => trim($_GET['q']           ?? ''),
            'categoria'   => trim($_GET['categoria']   ?? ''),
            'precio_min'  => $_GET['precio_min']  !== '' ? (float)$_GET['precio_min']  : null,
            'precio_max'  => $_GET['precio_max']  !== '' ? (float)$_GET['precio_max']  : null,
            'orden'       => trim($_GET['orden']       ?? 'mas_recientes'),
            'con_descuento' => !empty($_GET['con_descuento']) ? 1 : 0,
        ];

        // Eliminar nulls para la consulta
        $filters = array_filter($filters, fn($v) => $v !== null && $v !== '');

        // Validar rango de precio
        $pmin = isset($filters['precio_min']) ? (float)$filters['precio_min'] : null;
        $pmax = isset($filters['precio_max']) ? (float)$filters['precio_max'] : null;
        $precioError = ($pmin !== null && $pmax !== null && $pmin > $pmax);

        $page     = max(1, (int)($_GET['pagina'] ?? 1));
        $perPage  = 24;
        $offset   = ($page - 1) * $perPage;

        $productos = $precioError ? [] : ProductModel::search($filters, $perPage, $offset);
        $total     = $precioError ? 0  : ProductModel::searchCount($filters);
        $pages     = (int)ceil($total / $perPage);

        // Agregar imágenes principales
        foreach ($productos as &$prod) {
            $img = ProductModel::getMainImage((int)$prod['id']);
            $prod['imagen_principal'] = $img['ruta'] ?? null;
        }
        unset($prod);

        // Si es petición AJAX → retornar JSON con HTML parcial
        if ($this->isAjax()) {
            ob_start();
            include APP_PATH . '/views/catalog/partials/products_grid.php';
            $html = ob_get_clean();

            $this->json([
                'html'        => $html,
                'total'       => $total,
                'pages'       => $pages,
                'currentPage' => $page,
                'precioError' => $precioError,
            ]);
            return;
        }

        // Petición normal → vista completa
        $categorias    = CategoryModel::findActive();
        $navCategorias = $categorias;

        $this->render('catalog/index', [
            'pageTitle'    => 'Buscar joyas',
            'productos'    => $productos,
            'categorias'   => $categorias,
            'navCategorias'=> $navCategorias,
            'filters'      => $filters,
            'total'        => $total,
            'pages'        => $pages,
            'currentPage'  => $page,
            'precioError'  => $precioError,
        ]);
    }
}
