<?php
/**
 * Partial: Header VILUNA
 * Logo grande centrado arriba + menú horizontal debajo (hamburguesa solo en móvil)
 */
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'cantidad')) : 0;
$isLoggedIn = !empty($_SESSION['user_id']);
$isAdmin = ($isLoggedIn && ($_SESSION['user_rol'] ?? '') === 'admin');
$logoSrc = $logoPrincipalUrl ?? asset('images/logo.svg');
?>

<header class="viluna-header fixed-top" id="mainNav">
  <!-- Fila 1: Logo centrado grande + iconos derecha -->
  <div class="viluna-logo-bar">
    <div class="container d-flex align-items-center justify-content-center position-relative">
      <!-- Logo centrado -->
      <a href="<?= url() ?>" class="viluna-logo">
        <img src="<?= e($logoSrc) ?>" alt="VILUNA Joyería">
      </a>
      <!-- Iconos derecha (absoluto) -->
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

  <!-- Fila 2: Menú horizontal (visible en desktop, hamburguesa en móvil) -->
  <nav class="viluna-nav-bar" aria-label="Navegación principal">
    <div class="container">
      <!-- Hamburguesa solo en móvil -->
      <button class="viluna-mobile-toggle d-lg-none" id="mobileMenuToggle" aria-label="Abrir menú">
        <i class="bi bi-list"></i>
      </button>
      <!-- Links -->
      <ul class="viluna-nav-links" id="navLinks">
        <li><a href="<?= url() ?>">Inicio</a></li>
        <li><a href="<?= url('catalogo') ?>">Catálogo</a></li>
        <?php if (!empty($navCategorias)):
          foreach ($navCategorias as $cat): ?>
            <li><a href="<?= url('catalogo/' . $cat['slug']) ?>"><?= e($cat['nombre']) ?></a></li>
        <?php endforeach; endif; ?>
      </ul>
    </div>
  </nav>
</header>

<script>
(function(){
  var btn = document.getElementById('mobileMenuToggle');
  var nav = document.getElementById('navLinks');
  if(!btn||!nav) return;
  btn.addEventListener('click', function(){
    nav.classList.toggle('open');
    btn.querySelector('i').className = nav.classList.contains('open') ? 'bi bi-x-lg' : 'bi bi-list';
  });
})();
</script>
