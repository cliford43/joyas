<?php
/**
 * Partial: Navbar principal de VILUNA
 */
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'cantidad')) : 0;
$isLoggedIn = !empty($_SESSION['user_id']);
$isAdmin = ($isLoggedIn && ($_SESSION['user_rol'] ?? '') === 'admin');
$logoSrc = $logoPrincipalUrl ?? asset('images/logo.svg');
?>
<nav class="navbar navbar-viluna navbar-expand-lg fixed-top" id="mainNav" aria-label="Navegación principal">
  <div class="container">
    <!-- Logo -->
    <a class="navbar-brand" href="<?= url() ?>">
      <img src="<?= e($logoSrc) ?>" alt="VILUNA Joyería" height="48">
    </a>

    <!-- Toggle móvil -->
    <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#navMain"
            aria-controls="navMain" aria-expanded="false" aria-label="Abrir menú">
      <span style="color:var(--color-gold);font-size:1.4rem;">&#9776;</span>
    </button>

    <!-- Links -->
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="<?= url() ?>">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('catalogo') ?>">Catálogo</a></li>
        <?php
        // Mostrar categorías activas
        if (!empty($navCategorias)):
          foreach ($navCategorias as $cat): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= url('catalogo/' . $cat['slug']) ?>"><?= e($cat['nombre']) ?></a>
            </li>
        <?php endforeach; endif; ?>
      </ul>

      <!-- Acciones derecha -->
      <div class="d-flex align-items-center gap-3">
        <!-- Buscador rápido -->
        <a href="<?= url('buscar') ?>" class="nav-link text-white" aria-label="Buscar productos">
          <i class="bi bi-search"></i>
        </a>

        <!-- Carrito -->
        <a href="<?= url('carrito') ?>" class="nav-link text-white position-relative" aria-label="Carrito">
          <i class="bi bi-bag"></i>
          <?php if ($cartCount > 0): ?>
            <span class="cart-badge"><?= $cartCount ?></span>
          <?php endif; ?>
        </a>

        <!-- Usuario -->
        <?php if ($isLoggedIn): ?>
          <div class="dropdown">
            <a class="nav-link text-white dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-label="Mi cuenta">
              <i class="bi bi-person-circle"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" style="border:var(--border-gold);border-radius:0;">
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
          <a href="<?= url('login') ?>" class="btn-gold btn px-3 py-1">Ingresar</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
