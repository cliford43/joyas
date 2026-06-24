<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\CategoryModel;
use App\Models\ProductModel;

/**
 * CatalogController — Listado de productos y categorías.
 */
class CatalogController extends Controller
{
    /** GET /catalogo — Catálogo general con buscador */
    public function index(): void
    {
        $categorias    = CategoryModel::findActive();
        $navCategorias = $categorias;

        $filters = $this->getFilters();
        $page    = max(1, (int)($_GET['pagina'] ?? 1));
        $perPage = 24;
        $offset  = ($page - 1) * $perPage;

        $productos = ProductModel::search($filters, $perPage, $offset);
        $total     = ProductModel::searchCount($filters);

        foreach ($productos as &$prod) {
            $img = ProductModel::getMainImage((int)$prod['id']);
            $prod['imagen_principal'] = $img['ruta'] ?? null;
        }
        unset($prod);

        $this->render('catalog/index', [
            'pageTitle'     => 'Catálogo de joyas',
            'metaDescription' => 'Explora nuestra colección completa de joyería fina VILUNA.',
            'categorias'    => $categorias,
            'navCategorias' => $navCategorias,
            'productos'     => $productos,
            'filters'       => $filters,
            'total'         => $total,
            'pages'         => (int)ceil($total / $perPage),
            'currentPage'   => $page,
            'precioError'   => false,
            'cardSection'   => 'catalogo',
        ]);
    }

    /** GET /catalogo/{categoria} — Productos de una categoría */
    public function category(string $categoria): void
    {
        $cat = CategoryModel::findBySlug($categoria);
        if (!$cat || !(int)$cat['activo']) {
            (new ErrorController())->notFound();
            return;
        }

        $categorias    = CategoryModel::findActive();
        $navCategorias = $categorias;

        $page    = max(1, (int)($_GET['pagina'] ?? 1));
        $perPage = 24;
        $offset  = ($page - 1) * $perPage;

        $filters = $this->getFilters();
        $filters['categoria'] = $categoria;

        $productos = ProductModel::search($filters, $perPage, $offset);
        $total     = ProductModel::searchCount($filters);

        foreach ($productos as &$prod) {
            $img = ProductModel::getMainImage((int)$prod['id']);
            $prod['imagen_principal'] = $img['ruta'] ?? null;
        }
        unset($prod);

        $this->render('catalog/index', [
            'pageTitle'       => $cat['nombre'] . ' — VILUNA',
            'metaDescription' => $cat['descripcion'] ?? 'Colección de ' . $cat['nombre'] . ' en VILUNA.',
            'categorias'      => $categorias,
            'navCategorias'   => $navCategorias,
            'categoriaActual' => $cat,
            'productos'       => $productos,
            'filters'         => $filters,
            'total'           => $total,
            'pages'           => (int)ceil($total / $perPage),
            'currentPage'     => $page,
            'precioError'     => false,
            'cardSection'     => 'catalogo',
        ]);
    }

    private function getFilters(): array
    {
        return array_filter([
            'q'           => trim($_GET['q']         ?? ''),
            'categoria'   => trim($_GET['categoria'] ?? ''),
            'precio_min'  => isset($_GET['precio_min']) && $_GET['precio_min'] !== '' ? (float)$_GET['precio_min'] : null,
            'precio_max'  => isset($_GET['precio_max']) && $_GET['precio_max'] !== '' ? (float)$_GET['precio_max'] : null,
            'orden'       => trim($_GET['orden']     ?? 'mas_recientes'),
            'con_descuento' => !empty($_GET['con_descuento']) ? 1 : 0,
        ], fn($v) => $v !== null && $v !== '');
    }
}
