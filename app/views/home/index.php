<?php /* Vista: Página principal VILUNA */ ?>

<!-- ─── Hero Banner ──────────────────────────────────────────── -->
<section class="hero-banner" aria-label="Banner principal">
  <div class="overlay"></div>
  <div class="container hero-content">
    <div class="col-lg-7">
      <p class="tagline">Joyería fina desde <?= date('Y') - 10 ?></p>
      <h1>Elegancia que<br><span>perdura para siempre</span></h1>
      <p>Descubre nuestra colección exclusiva de joyas artesanales creadas con los mejores materiales y técnicas del mundo.</p>
      <div class="d-flex gap-3 flex-wrap">
        <a href="<?= url('catalogo') ?>" class="btn btn-gold">Explorar colección</a>
        <a href="<?= url('catalogo/anillos') ?>" class="btn btn-outline-gold">Ver anillos</a>
      </div>
    </div>
  </div>
  <div class="hero-scroll">
    <i class="bi bi-chevron-down d-block mb-1"></i>
    Descubre
  </div>
</section>

<!-- ─── Categorías ───────────────────────────────────────────── -->
<?php if (!empty($categorias)): ?>
<section class="py-5 bg-light-viluna" aria-labelledby="cats-title">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title" id="cats-title">Nuestras Colecciones</h2>
      <p class="section-subtitle">Encuentra la joya perfecta para cada ocasión</p>
    </div>
    <div class="row g-3">
      <?php foreach ($categorias as $cat): ?>
        <div class="col-6 col-md-4 col-lg-3">
          <a href="<?= url('catalogo/' . $cat['slug']) ?>" class="category-card d-block text-decoration-none"
             aria-label="Ver <?= e($cat['nombre']) ?>">
            <?php if (!empty($cat['imagen'])): ?>
              <img src="<?= e(mediaUrl((string)$cat['imagen'])) ?>" alt="<?= e($cat['nombre']) ?>" loading="lazy">
            <?php else: ?>
              <div style="background:linear-gradient(135deg,#1a1a1a,#333);width:100%;height:100%;min-height:200px;"></div>
            <?php endif; ?>
            <div class="overlay"></div>
            <div class="label"><?= e($cat['nombre']) ?></div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ─── Productos Destacados ─────────────────────────────────── -->
<?php if (!empty($destacados)): ?>
<section class="py-5" aria-labelledby="destacados-title">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title" id="destacados-title">Piezas Destacadas</h2>
      <p class="section-subtitle">Selección especial de nuestros artesanos</p>
    </div>
    <div class="row g-4">
      <?php foreach ($destacados as $prod): ?>
        <?php include __DIR__ . '/../partials/product_card.php'; ?>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
      <a href="<?= url('catalogo') ?>" class="btn btn-outline-gold">Ver todo el catálogo</a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ─── Productos Más Vendidos ────────────────────────────────── -->
<?php if (!empty($masVendidos)): ?>
<section class="py-5 bg-light-viluna" aria-labelledby="vendidos-title">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title" id="vendidos-title">Los Más Queridos</h2>
      <p class="section-subtitle">Las joyas favoritas de nuestros clientes</p>
    </div>
    <div class="row g-4">
      <?php foreach ($masVendidos as $prod): ?>
        <?php include __DIR__ . '/../partials/product_card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ─── Productos Nuevos ──────────────────────────────────────── -->
<?php if (!empty($nuevos)): ?>
<section class="py-5" aria-labelledby="nuevos-title">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title" id="nuevos-title">Recién Llegadas</h2>
      <p class="section-subtitle">Las últimas incorporaciones a nuestra colección</p>
    </div>
    <div class="row g-4">
      <?php foreach ($nuevos as $prod): ?>
        <?php include __DIR__ . '/../partials/product_card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ─── Banner informativo ───────────────────────────────────── -->
<section class="py-5" style="background:var(--color-black);">
  <div class="container">
    <div class="row g-4 text-center">
      <div class="col-md-4">
        <i class="bi bi-gem" style="font-size:2rem;color:var(--color-gold);"></i>
        <h5 class="mt-3" style="color:#fff;font-family:var(--font-heading);">Materiales Premium</h5>
        <p style="color:rgba(255,255,255,0.5);font-size:0.9rem;">Oro 18k, plata 925 y piedras naturales certificadas.</p>
      </div>
      <div class="col-md-4">
        <i class="bi bi-shield-check" style="font-size:2rem;color:var(--color-gold);"></i>
        <h5 class="mt-3" style="color:#fff;font-family:var(--font-heading);">Garantía Certificada</h5>
        <p style="color:rgba(255,255,255,0.5);font-size:0.9rem;">Todas nuestras piezas incluyen certificado de autenticidad.</p>
      </div>
      <div class="col-md-4">
        <i class="bi bi-box-seam" style="font-size:2rem;color:var(--color-gold);"></i>
        <h5 class="mt-3" style="color:#fff;font-family:var(--font-heading);">Envío Seguro</h5>
        <p style="color:rgba(255,255,255,0.5);font-size:0.9rem;">Empaque exclusivo y entrega asegurada a tu puerta.</p>
      </div>
    </div>
  </div>
</section>
