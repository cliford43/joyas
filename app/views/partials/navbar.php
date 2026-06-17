<?php
/**
 * Partial: Navbar principal de VILUNA
 * Diseño: Logo centrado arriba + menú hamburguesa siempre (móvil y escritorio)
 */
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'cantidad')) : 0;
$isLoggedIn = !empty($_SESSION['user_id']);
$isAdmin = ($isLoggedIn && ($_SESSION['user_rol'] ?? '') === 'admin');
$logoSrc = $logoPrincipalUrl ?? asset('images/logo.svg');
?>

<!-- Barra superior: Logo centrado -->
<header class="viluna-header fixed-top" id="mainNav">
  <!-- Logo arriba centrado -->
  <div class="viluna-logo-bar">
    <div class="container d-flex align-items-center justify-content-between">
      <!-- Hamburguesa izquierda -->
      <button class="viluna-hamburger" id="menuToggle" aria-label="Abrir menú" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>

      <!-- Logo centro -->
      <a href="<?= url() ?>" class="viluna-logo">
        <img src="<?= e($logoSrc) ?>" alt="VILUNA Joyería">
      </a>

      <!-- Acciones derecha -->
      <div class="viluna-header-actions">
        <a href="<?= url('buscar') ?>" aria-label="Buscar"><i class="bi bi-search"></i></a>
        <a href="<?= url('carrito') ?>" class="position-relative" aria-label="Carrito">
          <i class="bi bi-bag"></i>
          <?php if ($cartCount > 0): ?>
            <span class="cart-badge"><?= $cartCount ?></span>
          <?php endif; ?>
        </a>
        <?php if ($isLoggedIn): ?>
          <div class="dropdown">
            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-label="Mi cuenta">
              <i class="bi bi-person-circle"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="<?= url('mi-cuenta') ?>">Mi cuenta</a></li>
              <li><a class="dropdown-item" href="<?= url('mi-cuenta/ordenes') ?>">Mis órdenes</a></li>
              <li><a class="dropdown-item" href="<?= url('mi-cuenta/wishlist') ?>">Mi wishlist</a></li>
              <?php if ($isAdmin): ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-warning" href="<?= url('admin') ?>">Panel Admin</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>">Cerrar sesión</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="<?= url('login') ?>" aria-label="Ingresar"><i class="bi bi-person"></i></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>

<!-- Menú desplegable (overlay) -->
<div class="viluna-menu-overlay" id="menuOverlay">
  <nav class="viluna-menu-content" aria-label="Navegación principal">
    <ul>
      <li><a href="<?= url() ?>">Inicio</a></li>
      <li><a href="<?= url('catalogo') ?>">Catálogo</a></li>
      <?php if (!empty($navCategorias)):
        foreach ($navCategorias as $cat): ?>
          <li><a href="<?= url('catalogo/' . $cat['slug']) ?>"><?= e($cat['nombre']) ?></a></li>
      <?php endforeach; endif; ?>
    </ul>
    <div class="viluna-menu-footer">
      <?php if (!$isLoggedIn): ?>
        <a href="<?= url('login') ?>" class="btn btn-gold w-100">Ingresar</a>
        <a href="<?= url('registro') ?>" class="btn btn-outline-gold w-100 mt-2">Crear cuenta</a>
      <?php else: ?>
        <a href="<?= url('mi-cuenta') ?>" class="btn btn-outline-gold w-100">Mi cuenta</a>
      <?php endif; ?>
    </div>
  </nav>
</div>

<script>
(function(){
  const btn = document.getElementById('menuToggle');
  const overlay = document.getElementById('menuOverlay');
  const header = document.getElementById('mainNav');
  if(!btn||!overlay) return;
  btn.addEventListener('click', function(){
    const open = overlay.classList.toggle('open');
    btn.classList.toggle('active', open);
    btn.setAttribute('aria-expanded', open);
    document.body.style.overflow = open ? 'hidden' : '';
  });
  // Cerrar al hacer clic en un link
  overlay.querySelectorAll('a').forEach(function(a){
    a.addEventListener('click', function(){
      overlay.classList.remove('open');
      btn.classList.remove('active');
      btn.setAttribute('aria-expanded','false');
      document.body.style.overflow = '';
    });
  });
})();
</script>
