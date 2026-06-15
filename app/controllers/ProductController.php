<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\WishlistModel;

/**
 * ProductController — Página de detalle de producto.
 */
class ProductController extends Controller
{
    /** GET /producto/{slug} */
    public function show(string $slug): void
    {
        $producto = ProductModel::findBySlug($slug);
        if (!$producto) {
            (new ErrorController())->notFound();
            return;
        }

        $imagenes       = ProductModel::getImages((int)$producto['id']);
        $relacionados   = ProductModel::getRelated((int)$producto['categoria_id'], (int)$producto['id'], 4);
        $navCategorias  = CategoryModel::findActive();

        // Imagen principal
        $imagenPrincipal = null;
        foreach ($imagenes as $img) {
            if ((int)$img['es_principal']) {
                $imagenPrincipal = $img;
                break;
            }
        }
        if (!$imagenPrincipal && !empty($imagenes)) {
            $imagenPrincipal = $imagenes[0];
        }

        // Precio con descuento
        $precio      = (float)$producto['precio'];
        $descuento   = (float)$producto['descuento'];
        $precioFinal = max(0, $precio - $descuento);

        // Imágenes de relacionados
        foreach ($relacionados as &$rel) {
            $img = ProductModel::getMainImage((int)$rel['id']);
            $rel['imagen_principal'] = $img['ruta'] ?? null;
        }
        unset($rel);

        // Wishlist
        $inWishlist = false;
        if (!empty($_SESSION['user_id'])) {
            $inWishlist = WishlistModel::exists((int)$_SESSION['user_id'], (int)$producto['id']);
        }

        // Meta tags para SEO
        $metaImg = $imagenPrincipal ? APP_URL . '/' . ltrim($imagenPrincipal['ruta'], '/') : '';

        $this->render('product/show', [
            'pageTitle'       => $producto['nombre'],
            'metaDescription' => mb_substr(strip_tags($producto['descripcion']), 0, 160),
            'metaImage'       => $metaImg,
            'producto'        => $producto,
            'imagenes'        => $imagenes,
            'imagenPrincipal' => $imagenPrincipal,
            'relacionados'    => $relacionados,
            'precio'          => $precio,
            'descuento'       => $descuento,
            'precioFinal'     => $precioFinal,
            'inWishlist'      => $inWishlist,
            'navCategorias'   => $navCategorias,
        ]);
    }
}
