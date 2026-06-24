<?php
/* Vista: Página principal VILUNA */
$cfg = \App\Models\ConfigModel::getAll();
$heroTagline     = !empty($cfg['hero_tagline']) ? $cfg['hero_tagline'] : ('Joyería fina desde ' . (date('Y') - 10));
$heroTitulo      = !empty($cfg['hero_titulo']) ? $cfg['hero_titulo'] : 'Elegancia que perdura para siempre';
$heroDescripcion = !empty($cfg['hero_descripcion']) ? $cfg['hero_descripcion'] : 'Descubre nuestra colección exclusiva de joyas artesanales creadas con los mejores materiales y técnicas del mundo.';
?>

<!-- ─── Hero Banner (configurable: activar/desactivar desde Admin) ── -->
<?php
$heroActivo = ($cfg['hero_activo'] ?? '1') === '1';
$heroImage = !empty($cfg['hero_imagen']) ? '/' . ltrim($cfg['hero_imagen'], '/') : asset('images/hero-default.jpg');
$heroBgColor = !empty($cfg['hero_fondo_color']) ? $cfg['hero_fondo_color'] : '#111111';
?>
<?php if ($heroActivo): ?>
<section class="hero-banner" aria-label="Banner principal" style="background:<?= e($heroBgColor) ?>;">
  <div class="overlay"></div>
  <div class="container hero-content">
    <div class="row align-items-center g-5">
      <!-- Texto -->
      <div class="col-lg-6">
        <p class="tagline"><?= e($heroTagline) ?></p>
        <h1><?= nl2br(e($heroTitulo)) ?></h1>
        <p><?= e($heroDescripcion) ?></p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="<?= url('catalogo') ?>" class="btn btn-gold">Explorar colección</a>
          <a href="<?= url('catalogo/anillos') ?>" class="btn btn-outline-gold">Ver anillos</a>
        </div>
      </div>
      <!-- Imagen Hero (configurable desde Admin → Configuración) -->
      <div class="col-lg-6 d-none d-lg-block text-center">
        <img src="<?= e($heroImage) ?>" alt="Joyería VILUNA" class="hero-product-img"
             style="max-height:480px;width:auto;max-width:100%;filter:drop-shadow(0 20px 40px rgba(212,175,55,0.3));border-radius:8px;object-fit:cover;">
      </div>
    </div>
  </div>
  <div class="hero-scroll">
    <i class="bi bi-chevron-down d-block mb-1"></i>
    Descubre
  </div>
</section>
<?php endif; /* hero_activo */ ?>

<!-- ─── Secciones dinámicas ──────────────────────────────────── -->
<?php foreach ($sections as $idx => $section): ?>
<?php $bgClass = ($idx % 2 === 0) ? 'bg-light-viluna' : ''; ?>
<?php $sectionId = 'section-' . ($idx + 1); ?>

<?php if ($section['tipo'] === 'categories' && !empty($section['categorias'])): ?>
<section class="py-5 <?= $bgClass ?>" aria-labelledby="<?= $sectionId ?>">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title" id="<?= $sectionId ?>"><?= e($section['titulo']) ?></h2>
      <p class="section-subtitle"><?= e($section['descripcion']) ?></p>
    </div>
    <div class="row g-3">
      <?php foreach ($section['categorias'] as $cat): ?>
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

<?php elseif ($section['tipo'] === 'testimonials'): ?>
<?php if (!empty($section['testimonios'])): ?>
<?php
  $testimonios = $section['testimonios'];
  $titulo = $section['titulo'];
  $descripcion = $section['descripcion'];
  include __DIR__ . '/../partials/testimonials.php';
?>
<?php else: ?>
<section class="py-5 <?= $bgClass ?>" aria-labelledby="<?= $sectionId ?>">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title" id="<?= $sectionId ?>"><?= e($section['titulo']) ?></h2>
      <p class="section-subtitle"><?= e($section['descripcion']) ?></p>
    </div>
    <p class="text-center text-muted">Aún no hay suficientes testimonios para mostrar esta sección. Se necesitan al menos 3 reseñas aprobadas con 4 o 5 estrellas.</p>
  </div>
</section>
<?php endif; ?>

<?php elseif ($section['tipo'] === 'products' && !empty($section['productos'])): ?>
<section class="py-5 <?= $bgClass ?>" aria-labelledby="<?= $sectionId ?>">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title" id="<?= $sectionId ?>"><?= e($section['titulo']) ?></h2>
      <p class="section-subtitle"><?= e($section['descripcion']) ?></p>
    </div>
    <div class="row g-4">
      <?php foreach ($section['productos'] as $prod): ?>
        <?php $cardSection = 'home_sec'; $_homeSectionCards = $section['cards_por_fila'] ?? '4'; ?>
        <?php include __DIR__ . '/../partials/product_card.php'; ?>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
      <a href="<?= url('catalogo') ?>" class="btn btn-outline-gold">Ver todo el catálogo</a>
    </div>
  </div>
</section>
<?php endif; ?>

<?php endforeach; ?>
