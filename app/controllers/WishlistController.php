<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\WishlistModel;

/**
 * WishlistController — Toggle de productos en lista de deseos (AJAX).
 */
class WishlistController extends Controller
{
    /** POST /wishlist/toggle */
    public function toggle(): void
    {
        $productId = (int)($_POST['producto_id'] ?? 0);

        if ($productId <= 0) {
            $this->json(['success' => false, 'message' => 'Producto inválido.'], 422);
            return;
        }

        $inWishlist = WishlistModel::toggle($this->userId(), $productId);

        $this->json([
            'success'    => true,
            'inWishlist' => $inWishlist,
            'message'    => $inWishlist ? 'Agregado a wishlist.' : 'Eliminado de wishlist.',
        ]);
    }
}
