<?php /* Vista: Wishlist del cliente */ ?>
<div class="container py-5">
  <div class="row g-4">
    <div class="col-lg-3"><?php include APP_PATH . '/views/client/partials/sidebar.php'; ?></div>
    <div class="col-lg-9">
      <h1 class="font-heading mb-4">Mi lista de deseos
        <span class="small text-gold" style="font-size:1rem;">(<?= count($productos ?? []) ?>)</span>
      </h1>

      <?php if (empty($productos)): ?>
        <div class="text-center py-5">
          <i class="bi bi-heart" style="font-size:3rem;color:rgba(212,175,55,0.3);"></i>
          <p class="mt-3 text-muted">Tu lista de deseos está vacía.</p>
          <a href="<?= url('catalogo') ?>" class="btn btn-gold mt-2">Descubrir joyas</a>
        </div>
      <?php else: ?>
        <div class="row g-4">
          <?php foreach ($productos as $prod): ?>
            <?php
            $precioFinal = max(0, (float)$prod['precio'] - (float)$prod['descuento']);
            $imgUrl = $prod['imagen_principal']
                ? '/' . ltrim($prod['imagen_principal'], '/')
                : asset('images/placeholder-joya.jpg');
            ?>
            <div class="col-6 col-md-4">
              <article class="product-card h-100">
                <div class="img-wrap">
                  <a href="<?= url('producto/' . $prod['slug']) ?>">
                    <img src="<?= e($imgUrl) ?>" alt="<?= e($prod['nombre']) ?>" loading="lazy">
                  </a>
                </div>
                <div class="card-body d-flex flex-column">
                  <div class="category-tag"><?= e($prod['categoria_nombre']) ?></div>
                  <h3 class="card-title flex-grow-1">
                    <a href="<?= url('producto/' . $prod['slug']) ?>" class="text-black text-decoration-none">
                      <?= e($prod['nombre']) ?>
                    </a>
                  </h3>
                  <div class="price-block mb-3">
                    <span class="price-current">S/ <?= number_format($precioFinal, 2) ?></span>
                  </div>
                  <div class="d-flex gap-2">
                    <?php if ((int)$prod['stock'] > 0): ?>
                      <form method="POST" action="<?= url('carrito/agregar') ?>" class="flex-grow-1">
                        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                        <input type="hidden" name="producto_id" value="<?= (int)$prod['id'] ?>">
                        <input type="hidden" name="cantidad" value="1">
                        <button type="submit" class="btn btn-dark-viluna w-100 btn-sm">
                          <i class="bi bi-bag-plus me-1"></i>Al carrito
                        </button>
                      </form>
                    <?php else: ?>
                      <button class="btn btn-secondary w-100 btn-sm" disabled>Sin stock</button>
                    <?php endif; ?>
                    <button class="btn btn-outline-danger btn-sm action-btn"
                            data-wishlist="<?= (int)$prod['id'] ?>"
                            style="border-radius:0;width:auto;height:auto;padding:.4rem .7rem;"
                            aria-label="Quitar de wishlist">
                      <i class="bi bi-heart-fill"></i>
                    </button>
                  </div>
                </div>
              </article>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
