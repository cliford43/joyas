<!DOCTYPE html>
<html lang="es">
<head>
  <?php
    $siteConfig = \App\Models\ConfigModel::getAll();
    $logoConfig = trim((string)($siteConfig['logo_principal'] ?? ''));
    $logoPrincipalUrl = $logoConfig !== '' ? mediaUrl($logoConfig) : asset('images/logo.svg');
    $themeVars = themeCssVariables($siteConfig);
  ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' — Admin VILUNA' : 'Panel Admin — VILUNA' ?></title>
  <meta name="robots" content="noindex, nofollow">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= asset('css/custom.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
  <style>:root { <?= $themeVars ?> }</style>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <?php if (!empty($extraCss)): echo $extraCss; endif; ?>
</head>
<body class="admin-body">

  <!-- Header -->
  <header class="admin-header">
    <button class="admin-sidebar-toggle" id="sidebarToggle" aria-label="Abrir sidebar">
      <i class="bi bi-list"></i>
    </button>
    <a href="<?= url('admin') ?>" class="brand">
      <img src="<?= e($logoPrincipalUrl) ?>" alt="VILUNA">
      <span>Admin</span>
    </a>
    <div class="header-right">
      <a href="<?= url() ?>" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>Ver tienda</a>
      <a href="<?= url('logout') ?>" style="color:rgba(220,53,69,0.8);"><i class="bi bi-power"></i></a>
    </div>
  </header>

  <!-- Sidebar -->
  <nav class="admin-sidebar" id="adminSidebar" aria-label="Menú administrativo">
    <div class="sidebar-section">Principal</div>
    <a href="<?= url('admin') ?>" class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="sidebar-section">Catálogo</div>
    <a href="<?= url('admin/categorias') ?>" class="nav-link <?= ($currentPage ?? '') === 'categorias' ? 'active' : '' ?>">
      <i class="bi bi-grid-3x3-gap"></i> Categorías
    </a>
    <a href="<?= url('admin/productos') ?>" class="nav-link <?= ($currentPage ?? '') === 'productos' ? 'active' : '' ?>">
      <i class="bi bi-gem"></i> Productos
    </a>

    <div class="sidebar-section">Ventas</div>
    <a href="<?= url('admin/ordenes') ?>" class="nav-link <?= ($currentPage ?? '') === 'ordenes' ? 'active' : '' ?>">
      <i class="bi bi-receipt"></i> Órdenes
    </a>
    <a href="<?= url('admin/pagos') ?>" class="nav-link <?= ($currentPage ?? '') === 'pagos' ? 'active' : '' ?>">
      <i class="bi bi-credit-card"></i> Pagos (Transferencia)
    </a>

    <div class="sidebar-section">Marketing</div>
    <a href="<?= url('admin/cupones') ?>" class="nav-link <?= ($currentPage ?? '') === 'cupones' ? 'active' : '' ?>">
      <i class="bi bi-tag"></i> Cupones
    </a>
    <a href="<?= url('admin/newsletter') ?>" class="nav-link <?= ($currentPage ?? '') === 'newsletter' ? 'active' : '' ?>">
      <i class="bi bi-envelope-heart"></i> Newsletter
    </a>

    <div class="sidebar-section">Gestión</div>
    <a href="<?= url('admin/usuarios') ?>" class="nav-link <?= ($currentPage ?? '') === 'usuarios' ? 'active' : '' ?>">
      <i class="bi bi-people"></i> Usuarios
    </a>
    <a href="<?= url('admin/configuracion') ?>" class="nav-link <?= ($currentPage ?? '') === 'configuracion' ? 'active' : '' ?>">
      <i class="bi bi-sliders"></i> Configuración
    </a>
  </nav>

  <!-- Main Content -->
  <main class="admin-main" id="adminMain">

    <!-- Flash messages -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
      <div class="alert alert-success alert-dismissible fade show border-0" role="alert"
           style="border-left:3px solid var(--color-gold)!important;">
        <?= e($_SESSION['flash_success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
      <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show border-0" role="alert">
        <?= e($_SESSION['flash_error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <?= $content ?>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <script src="<?= asset('js/admin.js') ?>"></script>

  <script>
    // Toggle sidebar en móvil
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      document.getElementById('adminSidebar').classList.toggle('open');
    });
  </script>
  <?php if (!empty($extraJs)): echo $extraJs; endif; ?>
</body>
</html>
