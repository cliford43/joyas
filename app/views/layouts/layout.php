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

  <!-- SEO Meta Tags dinámicos -->
  <title><?= isset($pageTitle) ? e($pageTitle) . ' — VILUNA' : 'VILUNA — Joyería Fina' ?></title>
  <meta name="description" content="<?= isset($metaDescription) ? e($metaDescription) : 'VILUNA — Joyería fina y exclusiva. Descubre nuestra colección de anillos, collares, pulseras y más.' ?>">
  <?php if (!empty($metaImage)): ?>
    <meta property="og:image" content="<?= e($metaImage) ?>">
  <?php endif; ?>
  <meta property="og:title" content="<?= isset($pageTitle) ? e($pageTitle) . ' — VILUNA' : 'VILUNA — Joyería Fina' ?>">
  <meta property="og:description" content="<?= isset($metaDescription) ? e($metaDescription) : 'VILUNA — Joyería fina y exclusiva.' ?>">
  <meta property="og:type" content="website">
  <meta name="robots" content="<?= isset($metaRobots) ? e($metaRobots) : 'index, follow' ?>">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">
  <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.svg') ?>">

  <!-- Bootstrap 5 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Cormorant+Garamond:wght@400;600&family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= asset('css/custom.css') ?>">
  <style>:root { <?= $themeVars ?> }</style>

  <?php if (!empty($extraCss)): echo $extraCss; endif; ?>
</head>
<body>

  <?php require APP_PATH . '/views/partials/navbar.php'; ?>

  <!-- Espacio para navbar fija -->
  <div style="height:80px;" aria-hidden="true"></div>

  <!-- Flash messages -->
  <?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="container mt-3">
      <div class="alert-viluna-success d-flex justify-content-between align-items-center">
        <span><?= e($_SESSION['flash_success']) ?></span>
        <button type="button" onclick="this.parentElement.remove()" aria-label="Cerrar"
                style="background:none;border:none;cursor:pointer;">&times;</button>
      </div>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
  <?php endif; ?>
  <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="container mt-3">
      <div class="alert-viluna-error d-flex justify-content-between align-items-center">
        <span><?= e($_SESSION['flash_error']) ?></span>
        <button type="button" onclick="this.parentElement.remove()" aria-label="Cerrar"
                style="background:none;border:none;cursor:pointer;">&times;</button>
      </div>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
  <?php endif; ?>

  <!-- Contenido principal -->
  <main id="main-content">
    <?= $content ?>
  </main>

  <?php require APP_PATH . '/views/partials/footer.php'; ?>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <!-- App JS -->
  <script src="<?= asset('js/app.js') ?>"></script>
  <?php if (!empty($extraJs)): echo $extraJs; endif; ?>
</body>
</html>
