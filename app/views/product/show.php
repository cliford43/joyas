<?php
/* Vista: Detalle de producto */
$sinStock    = (int)($producto['stock'] ?? 0) === 0;
$tieneDesc   = (float)($descuento ?? 0) > 0;
$imgPrincipal = $imagenPrincipal['ruta'] ?? null;
$imgUrl      = mediaUrl($imgPrincipal);
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
                <img src="<?= e(mediaUrl((string)$img['ruta'])) ?>"
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
            <?= formatPrice((float)($precioFinal ?? 0)) ?>
          </span>
          <?php if ($tieneDesc): ?>
            <span style="font-size:1.1rem;color:var(--color-gray);text-decoration:line-through;">
              <?= formatPrice((float)($precio ?? 0)) ?>
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
              <button type="submit" class="btn btn-gold" data-loading-text="Agregando...">
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

    <!-- ─── Reseñas ──────────────────────────────────────── -->
    <div class="mt-5 pt-4" style="border-top:var(--border-gold);">
      <h2 class="section-title text-start mb-4" style="display:block;">Reseñas de clientes</h2>

      <!-- Resumen de calificaciones -->
      <div class="d-flex align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-1">
          <?php
            $promedio = (float)($reviewStats['promedio'] ?? 0);
            for ($i = 1; $i <= 5; $i++):
              if ($i <= floor($promedio)):
          ?>
            <i class="bi bi-star-fill" style="color:var(--color-gold);font-size:1.2rem;"></i>
          <?php elseif ($i - $promedio < 1 && $i - $promedio > 0): ?>
            <i class="bi bi-star-half" style="color:var(--color-gold);font-size:1.2rem;"></i>
          <?php else: ?>
            <i class="bi bi-star" style="color:var(--color-gold);font-size:1.2rem;"></i>
          <?php endif; endfor; ?>
        </div>
        <span style="font-size:1.3rem;font-weight:600;"><?= number_format($promedio, 1) ?></span>
        <span style="color:var(--color-gray);">
          (<?= (int)($reviewStats['total_valoraciones'] ?? 0) ?> <?= (int)($reviewStats['total_valoraciones'] ?? 0) === 1 ? 'valoración' : 'valoraciones' ?>)
        </span>
      </div>

      <!-- Lista de reseñas -->
      <?php if (!empty($resenas)): ?>
        <div class="review-list">
          <?php foreach ($resenas as $resena): ?>
            <div class="review-item mb-4 pb-4" style="border-bottom:1px solid #eee;">
              <div class="d-flex align-items-center gap-2 mb-2">
                <strong><?= e($resena['usuario_nombre'] ?? 'Usuario') ?></strong>
                <span class="d-flex gap-0">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="bi <?= $i <= (int)($resena['calificacion'] ?? 0) ? 'bi-star-fill' : 'bi-star' ?>"
                       style="color:var(--color-gold);font-size:0.85rem;"></i>
                  <?php endfor; ?>
                </span>
                <span style="color:var(--color-gray);font-size:0.8rem;">
                  <?= date('d/m/Y', strtotime($resena['fecha_creacion'] ?? 'now')) ?>
                </span>
              </div>
              <p class="mb-0" style="color:var(--color-gray);line-height:1.6;">
                <?= e($resena['comentario'] ?? '') ?>
              </p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p style="color:var(--color-gray);">No hay reseñas aún. ¡Sé el primero en opinar!</p>
      <?php endif; ?>

      <!-- Formulario de reseña -->
      <?php
        $flashSuccess = $_SESSION['flash_success'] ?? null;
        $flashError   = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
      ?>

      <?php if ($flashSuccess): ?>
        <div class="alert alert-success mt-4" role="alert">
          <i class="bi bi-check-circle me-1"></i><?= e($flashSuccess) ?>
        </div>
      <?php endif; ?>

      <?php if ($flashError): ?>
        <div class="alert alert-danger mt-4" role="alert">
          <i class="bi bi-exclamation-circle me-1"></i><?= e($flashError) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($_SESSION['user_id']) && !empty($canReview)): ?>
        <div class="mt-4 pt-4" style="border-top:1px solid #eee;">
          <h5 class="mb-3">Deja tu reseña</h5>
          <form method="POST" action="<?= url('producto/' . ($producto['slug'] ?? '') . '/resena') ?>" id="reviewForm">
            <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

            <!-- Star rating selector -->
            <div class="mb-3">
              <label class="form-label">Calificación <span class="text-danger">*</span></label>
              <div class="star-rating-input" id="starRatingInput" role="radiogroup" aria-label="Calificación de 1 a 5 estrellas">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <i class="bi bi-star star-selectable" data-value="<?= $i ?>" role="radio"
                     aria-label="<?= $i ?> estrella<?= $i > 1 ? 's' : '' ?>"
                     aria-checked="false" tabindex="0"
                     style="font-size:1.5rem;cursor:pointer;color:var(--color-gold);transition:transform 0.1s;"></i>
                <?php endfor; ?>
              </div>
              <input type="hidden" name="calificacion" id="calificacionInput"
                     value="<?= e($reviewOld['calificacion'] ?? '') ?>">
              <?php if (!empty($reviewErrors['calificacion'])): ?>
                <div class="text-danger small mt-1"><?= e($reviewErrors['calificacion']) ?></div>
              <?php endif; ?>
            </div>

            <!-- Comment textarea -->
            <div class="mb-3">
              <label for="comentarioInput" class="form-label">Comentario <span class="text-danger">*</span></label>
              <textarea name="comentario" id="comentarioInput" rows="4"
                        class="form-control <?= !empty($reviewErrors['comentario']) ? 'is-invalid' : '' ?>"
                        placeholder="Comparte tu experiencia con este producto (mín. 10 caracteres)"
                        minlength="10" maxlength="1000"><?= e($reviewOld['comentario'] ?? '') ?></textarea>
              <?php if (!empty($reviewErrors['comentario'])): ?>
                <div class="invalid-feedback"><?= e($reviewErrors['comentario']) ?></div>
              <?php endif; ?>
              <div class="form-text">Mínimo 10 caracteres, máximo 1000.</div>
            </div>

            <button type="submit" class="btn btn-gold" data-loading-text="Enviando...">
              <i class="bi bi-send me-1"></i>Enviar reseña
            </button>
          </form>
        </div>
      <?php elseif (!empty($_SESSION['user_id']) && !empty($userHasReview)): ?>
        <p class="mt-4" style="color:var(--color-gray);font-style:italic;">
          <i class="bi bi-check-circle me-1"></i>Ya has publicado una reseña para este producto.
        </p>
      <?php elseif (empty($_SESSION['user_id'])): ?>
        <p class="mt-4" style="color:var(--color-gray);">
          <a href="<?= url('login') ?>" class="text-gold">Inicia sesión</a> para dejar una reseña.
        </p>
      <?php endif; ?>
    </div>

    <!-- ─── Productos Relacionados ───────────────────────── -->
    <?php if (!empty($relacionados)): ?>
    <div class="mt-5 pt-4" style="border-top:var(--border-gold);">
      <h2 class="section-title text-start mb-4" style="display:block;">También te puede interesar</h2>
      <div class="row g-4">
        <?php foreach ($relacionados as $prod): ?>
          <?php $cardSection = 'catalogo'; ?>
          <?php include APP_PATH . '/views/partials/product_card.php'; ?>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</section>
