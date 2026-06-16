<?php
/**
 * Partial: Sección de testimonios destacados para la home.
 * Requiere variable $testimonios (array de reseñas aprobadas con calificación >= 4).
 * Requiere variable $titulo y $descripcion para el encabezado.
 * Requiere variable $sectionId para accesibilidad.
 * Requiere variable $bgClass para alternar fondo.
 */
?>
<section class="py-5 <?= $bgClass ?? '' ?>" aria-labelledby="<?= $sectionId ?>">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title" id="<?= $sectionId ?>"><?= e($titulo) ?></h2>
      <p class="section-subtitle"><?= e($descripcion) ?></p>
    </div>
    <div class="row g-4">
      <?php foreach ($testimonios as $testimonio): ?>
        <div class="col-12 col-md-6 col-lg-4">
          <article class="testimonial-card h-100 p-4 bg-white rounded shadow-sm">
            <!-- Estrellas -->
            <div class="mb-2" aria-label="Calificación: <?= (int)$testimonio['calificacion'] ?> de 5 estrellas">
              <?php for ($s = 1; $s <= 5; $s++): ?>
                <i class="bi <?= $s <= (int)$testimonio['calificacion'] ? 'bi-star-fill text-warning' : 'bi-star text-muted' ?>"></i>
              <?php endfor; ?>
            </div>
            <!-- Comentario -->
            <blockquote class="mb-3">
              <p class="text-muted fst-italic">"<?= e($testimonio['comentario']) ?>"</p>
            </blockquote>
            <!-- Autor y producto -->
            <footer class="d-flex justify-content-between align-items-center">
              <span class="fw-semibold"><?= e($testimonio['usuario_nombre']) ?></span>
              <a href="<?= url('producto/' . ($testimonio['producto_slug'] ?? '')) ?>" class="text-decoration-none small text-gold">
                <?= e($testimonio['producto_nombre']) ?>
              </a>
            </footer>
          </article>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
