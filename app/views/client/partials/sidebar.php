<?php
/* Partial: Sidebar del dashboard cliente */
$currentUri = (new \Core\Router())->getCurrentUri();
?>
<nav class="sidebar-viluna rounded" aria-label="Menú de cuenta">
  <div class="p-3 text-center" style="border-bottom:1px solid rgba(212,175,55,0.15);">
    <i class="bi bi-person-circle" style="font-size:2.5rem;color:var(--color-gold);"></i>
    <div style="color:#fff;font-size:0.85rem;margin-top:0.4rem;">
      <?= e($_SESSION['user_nombre'] ?? '') ?>
    </div>
  </div>
  <div class="py-2">
    <a href="<?= url('mi-cuenta') ?>"
       class="nav-link <?= $currentUri === '/mi-cuenta' ? 'active' : '' ?>">
      <i class="bi bi-speedometer2"></i> Mi resumen
    </a>
    <a href="<?= url('mi-cuenta/perfil') ?>"
       class="nav-link <?= str_starts_with($currentUri, '/mi-cuenta/perfil') ? 'active' : '' ?>">
      <i class="bi bi-person"></i> Mi perfil
    </a>
    <a href="<?= url('mi-cuenta/contrasena') ?>"
       class="nav-link <?= str_starts_with($currentUri, '/mi-cuenta/contrasena') ? 'active' : '' ?>">
      <i class="bi bi-shield-lock"></i> Contraseña
    </a>
    <a href="<?= url('mi-cuenta/ordenes') ?>"
       class="nav-link <?= str_starts_with($currentUri, '/mi-cuenta/ordenes') ? 'active' : '' ?>">
      <i class="bi bi-receipt"></i> Mis órdenes
    </a>
    <a href="<?= url('mi-cuenta/wishlist') ?>"
       class="nav-link <?= str_starts_with($currentUri, '/mi-cuenta/wishlist') ? 'active' : '' ?>">
      <i class="bi bi-heart"></i> Mi wishlist
    </a>
    <hr style="border-color:rgba(212,175,55,0.15);margin:0.5rem 1rem;">
    <a href="<?= url('logout') ?>" class="nav-link" style="color:rgba(220,53,69,0.7)!important;">
      <i class="bi bi-box-arrow-left"></i> Cerrar sesión
    </a>
  </div>
</nav>
