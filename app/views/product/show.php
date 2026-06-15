<?php
/* Vista: Detalle de producto */
$sinStock    = (int)($producto['stock'] ?? 0) === 0;
$tieneDesc   = (float)($descuento ?? 0) > 0;
$imgPrincipal = $imagenPrincipal['ruta'] ?? null;
$imgUrl      = $imgPrincipal ? '/' . ltrim($imgPrincipal, '/') : asset('images/placeholder-joya.jpg');
?>

<!-- Breadcrumb -->
<div class="breadcrumb-viluna">
  <div class="container">
    <nav aria-label="Ruta de navegación">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url() ?>">Inicio</a></li>
        <li class="breadcrumb-item"><a href="<?= url('catalogo') ?>">Catálogo</a></li>
        <li class="breadcrumb-item">
          <a href="<?= url('catalogo/' . ($producto['categoria_slug'] ?? '')) ?>">
            <?= e($producto['categoria_nombre'] ?? '') ?>
          </a>
        </li>
        <li class="breadcrumb-item active"><?= e($producto['nombre'] ?? '') ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <div class="row g-5">

      <!-- ─── Galería ───────────────────────────────────── -->
      <div class="col-lg-6">
        <div class="product-gallery">
          <!-- Imagen principal con zoom -->
          <div class="main-image" aria-label="Imagen principal del producto">
            <img src="<?= e($imgUrl) ?>"
                 alt="<?= e($producto['nombre'] ?? '') ?>"
                 id="mainProductImg">
          </div>
          <!-- Miniaturas -->
          <?php if (!empty($imagenes) && count($imagenes) > 1): ?>
            <div class="thumbnails" role="list" aria-label="Miniaturas del producto">
              <?php foreach ($imagenes as $i => $img): ?>
                <img src="/<?= e(ltrim($img['ruta'], '/')) ?>"
                     alt="Vista <?= $i + 1 ?> — <?= e($producto['nombre'] ?? '') ?>"
                     class="thumb <?= (int)$img['es_principal'] ? 'active' : '' ?>"
                     role="listitem"
                     onclick="document.getElementById('mainProductImg').src=this.src;
                              document.querySelectorAll('.thumb').forEach(t=>t.classList.remove('active'));
                              this.classList.add('active');">
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- ─── Info del producto ─────────────────────────── -->
      <div class="col-lg-6">
        <!-- Categoría -->
        <a href="<?= url('catalogo/' . ($producto['categoria_slug'] ?? '')) ?>"
           class="small text-gold text-uppercase letter-spacing d-block mb-2">
          <?= e($producto['categoria_nombre'] ?? '') ?>
        </a>

        <h1 class="font-heading mb-3" style="font-size:clamp(1.5rem,3vw,2.2rem);">
          <?= e($producto['nombre'] ?? '') ?>
        </h1>

        <!-- Precio -->
        <div class="d-flex align-items-baseline gap-3 mb-4">
          <span style="font-size:2rem;font-weight:700;color:var(--color-black);">
            S/ <?= number_format($precioFinal ?? 0, 2) ?>
          </span>
          <?php if ($tieneDesc): ?>
            <span style="font-size:1.1rem;color:var(--color-gray);text-decoration:line-through;">
              S/ <?= number_format($precio ?? 0, 2) ?>
            </span>
            <span class="badge-discount" style="position:static;display:inline-block;">
              -<?= round((($descuento ?? 0) / ($precio ?: 1)) * 100) ?>%
            </span>
          <?php endif; ?>
        </div>

        <!-- Stock -->
        <div class="mb-4">
          <?php if ($sinStock): ?>
            <span class="badge-sin-stock" style="position:static;display:inline-block;">Sin stock</span>
          <?php else: ?>
            <span style="color:#198754;font-size:0.85rem;">
              <i class="bi bi-check-circle me-1"></i>
              Disponible — <?= (int)($producto['stock'] ?? 0) ?> en stock
            </span>
          <?php endif; ?>
        </div>

        <!-- Descripción -->
        <div class="mb-4" style="color:var(--color-gray);line-height:1.8;">
          <?= nl2br(e($producto['descripcion'] ?? '')) ?>
        </div>

        <!-- Acciones -->
        <div class="d-flex gap-3 flex-wrap mb-4">
          <?php if (!$sinStock): ?>
            <form method="POST" action="<?= url('carrito/agregar') ?>" class="d-flex gap-2 align-items-center">
              <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
              <input type="hidden" name="producto_id" value="<?= (int)$producto['id'] ?>">
              <input type="number" name="cantidad" value="1" min="1"
                     max="<?= (int)$producto['stock'] ?>"
                     class="qty-input" aria-label="Cantidad" style="width:70px;">
              <button type="submit" class="btn btn-gold">
                <i class="bi bi-bag-plus me-1"></i>Agregar al carrito
              </button>
            </form>
          <?php else: ?>
            <button class="btn btn-secondary" disabled aria-disabled="true">
              Sin stock
            </button>
          <?php endif; ?>

          <?php if (!empty($_SESSION['user_id'])): ?>
            <button class="btn btn-outline-gold action-btn <?= ($inWishlist ?? false) ? 'active' : '' ?>"
                    data-wishlist="<?= (int)$producto['id'] ?>"
                    style="border-radius:0;width:auto;height:auto;padding:.65rem 1.2rem;"
                    aria-label="<?= ($inWishlist ?? false) ? 'Quitar de wishlist' : 'Agregar a wishlist' ?>">
              <i class="bi <?= ($inWishlist ?? false) ? 'bi-heart-fill' : 'bi-heart' ?> me-1"></i>
              Wishlist
            </button>
          <?php endif; ?>
        </div>

        <!-- Meta info -->
        <div style="border-top:var(--border-gold);padding-top:1rem;font-size:0.85rem;color:var(--color-gray);">
          <strong>Categoría:</strong>
          <a href="<?= url('catalogo/' . ($producto['categoria_slug'] ?? '')) ?>" class="text-gold">
            <?= e($producto['categoria_nombre'] ?? '') ?>
          </a>
        </div>
      </div>
    </div>

    <!-- ─── Productos Relacionados ───────────────────────── -->
    <?php if (!empty($relacionados)): ?>
    <div class="mt-5 pt-4" style="border-top:var(--border-gold);">
      <h2 class="section-title text-start mb-4" style="display:block;">También te puede interesar</h2>
      <div class="row g-4">
        <?php foreach ($relacionados as $prod): ?>
          <?php include APP_PATH . '/views/partials/product_card.php'; ?>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</section>
