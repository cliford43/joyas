<?php
/**
 * Partial: Footer de VILUNA
 */
$config = $_SESSION['config'] ?? [];
$whatsapp = $config['whatsapp'] ?? '';
$facebook  = $config['facebook']  ?? '#';
$instagram = $config['instagram'] ?? '#';
$nombreTienda = $config['nombre_tienda'] ?? 'VILUNA';
$logoSrc = $logoPrincipalUrl ?? asset('images/logo.svg');
?>
<footer class="footer-viluna" role="contentinfo">
  <div class="container">
    <div class="row g-4">

      <!-- Marca -->
      <div class="col-lg-4">
        <a href="<?= url() ?>">
          <img src="<?= e($logoSrc) ?>" alt="<?= e($nombreTienda) ?>" height="50" class="mb-3">
        </a>
        <p class="small" style="color:rgba(255,255,255,0.55);max-width:280px;">
          Joyería fina y exclusiva. Cada pieza es una obra de arte creada con pasión y los mejores materiales.
        </p>
        <!-- Redes sociales -->
        <div class="d-flex gap-3 mt-3">
          <?php if ($facebook !== '#'): ?>
            <a href="<?= e($facebook) ?>" target="_blank" rel="noopener" aria-label="Facebook" style="color:var(--color-gold);font-size:1.1rem;">
              <i class="bi bi-facebook"></i>
            </a>
          <?php endif; ?>
          <?php if ($instagram !== '#'): ?>
            <a href="<?= e($instagram) ?>" target="_blank" rel="noopener" aria-label="Instagram" style="color:var(--color-gold);font-size:1.1rem;">
              <i class="bi bi-instagram"></i>
            </a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Navegación -->
      <div class="col-sm-6 col-lg-2">
        <h5>Tienda</h5>
        <a href="<?= url('catalogo') ?>">Catálogo</a>
        <a href="<?= url('catalogo/anillos') ?>">Anillos</a>
        <a href="<?= url('catalogo/collares') ?>">Collares</a>
        <a href="<?= url('catalogo/pulseras') ?>">Pulseras</a>
        <a href="<?= url('catalogo/aretes') ?>">Aretes</a>
      </div>

      <!-- Cuenta -->
      <div class="col-sm-6 col-lg-2">
        <h5>Mi Cuenta</h5>
        <a href="<?= url('login') ?>">Iniciar sesión</a>
        <a href="<?= url('registro') ?>">Registrarse</a>
        <a href="<?= url('mi-cuenta/ordenes') ?>">Mis órdenes</a>
        <a href="<?= url('carrito') ?>">Mi carrito</a>
      </div>

      <!-- Newsletter -->
      <div class="col-lg-4">
        <h5>Newsletter</h5>
        <p class="small mb-2" style="color:rgba(255,255,255,0.5);">
          Suscríbete y recibe ofertas exclusivas.
        </p>
        <form class="newsletter-form" id="newsletterForm" action="<?= url('newsletter/suscribir') ?>" method="POST"
              aria-label="Suscripción al newsletter">
          <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?? '' ?>">
          <input type="email" name="correo" placeholder="tu@correo.com"
                 required aria-label="Correo electrónico para newsletter">
          <button type="submit" aria-label="Suscribirse"><i class="bi bi-send"></i></button>
        </form>
        <div id="newsletterMsg" class="mt-2 small" aria-live="polite"></div>
      </div>

    </div>

    <hr class="footer-divider">
    <div class="footer-bottom">
      &copy; <?= date('Y') ?> <?= e($nombreTienda) ?> &mdash; Todos los derechos reservados.
    </div>
  </div>
</footer>

<?php require APP_PATH . '/views/partials/whatsapp_btn.php'; ?>
