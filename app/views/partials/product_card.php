<?php
/**
 * Partial: Tarjeta de producto reutilizable.
 * Requiere variable $prod con datos del producto.
 */
$precioFinal  = (float)($prod['precio'] ?? 0) - (float)($prod['descuento'] ?? 0);
$tieneDesc    = (float)($prod['descuento'] ?? 0) > 0;
$sinStock     = (int)($prod['stock'] ?? 0) === 0;
$imgSrc       = $prod['imagen_principal'] ?? null;
$imgUrl       = mediaUrl($imgSrc);
$inWishlist   = false;
if (!empty($_SESSION['user_id']) && isset($prod['id'])) {
    // Se verifica en el controlador si se quiere rendimiento máximo
    // Aquí dejamos el estado inicial en false para el ícono
}
?>
<div class="col-6 col-md-4 col-xl-3">
  <article class="product-card h-100">
    <!-- Badges -->
    <?php if ($sinStock): ?>
      <span class="badge-sin-stock">Sin stock</span>
    <?php elseif ($tieneDesc): ?>
      <span class="badge-discount">-<?= round(((float)$prod['descuento'] / (float)$prod['precio']) * 100) ?>%</span>
    <?php elseif (!empty($prod['es_nuevo'])): ?>
      <span class="badge-new">Nuevo</span>
    <?php endif; ?>

    <!-- Acciones rápidas -->
    <div class="card-actions" aria-label="Acciones rápidas">
      <?php if (!empty($_SESSION['user_id'])): ?>
        <button class="action-btn <?= $inWishlist ? 'active' : '' ?>"
                data-wishlist="<?= (int)$prod['id'] ?>"
                aria-label="<?= $inWishlist ? 'Quitar de wishlist' : 'Agregar a wishlist' ?>">
          <i class="bi <?= $inWishlist ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
        </button>
      <?php endif; ?>
    </div>

    <!-- Imagen -->
    <div class="img-wrap">
      <a href="<?= url('producto/' . ($prod['slug'] ?? '')) ?>">
        <img src="<?= e($imgUrl) ?>"
             alt="<?= e($prod['nombre'] ?? '') ?>"
             loading="lazy">
      </a>
    </div>

    <!-- Info -->
    <div class="card-body d-flex flex-column">
      <div class="category-tag"><?= e($prod['categoria_nombre'] ?? '') ?></div>
      <h3 class="card-title">
        <a href="<?= url('producto/' . ($prod['slug'] ?? '')) ?>" class="text-decoration-none text-black">
          <?= e($prod['nombre'] ?? '') ?>
        </a>
      </h3>
      <div class="price-block mb-3">
        <span class="price-current"><?= formatPrice($precioFinal) ?></span>
        <?php if ($tieneDesc): ?>
          <span class="price-original"><?= formatPrice((float)$prod['precio']) ?></span>
        <?php endif; ?>
      </div>

      <div class="product-card-cta">
        <?php if (!$sinStock): ?>
          <form method="POST" action="<?= url('carrito/agregar') ?>">
            <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?? '' ?>">
            <input type="hidden" name="producto_id" value="<?= (int)$prod['id'] ?>">
            <input type="hidden" name="cantidad" value="1">
            <button type="submit" class="btn btn-dark-viluna w-100 btn-sm">
              <i class="bi bi-bag-plus me-1"></i>Agregar
            </button>
          </form>
        <?php else: ?>
          <button class="btn btn-secondary w-100 btn-sm" disabled>Sin stock</button>
        <?php endif; ?>
      </div>
    </div>
  </article>
</div>
