<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>500 — Error del servidor | VILUNA</title>
  <meta name="robots" content="noindex">
  <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Raleway:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body style="background:#111;display:flex;align-items:center;justify-content:center;min-height:100vh;">
  <div class="text-center px-4">
    <div style="font-family:'Playfair Display',serif;font-size:8rem;font-weight:700;color:rgba(212,175,55,0.15);line-height:1;">500</div>
    <div style="margin-top:-2rem;">
      <h1 style="font-family:'Playfair Display',serif;color:#fff;font-size:1.8rem;margin-bottom:0.75rem;">
        Error del servidor
      </h1>
      <p style="color:rgba(255,255,255,0.5);font-size:1rem;max-width:360px;margin:0 auto 2rem;">
        Algo salió mal. Por favor intenta nuevamente en unos momentos.
      </p>
      <?php if (defined('APP_ENV') && APP_ENV === 'development' && !empty($message)): ?>
        <pre style="background:rgba(255,255,255,0.05);color:#D4AF37;text-align:left;padding:1rem;font-size:0.75rem;max-width:600px;margin:0 auto 2rem;overflow:auto;"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></pre>
      <?php endif; ?>
      <a href="/" class="btn-gold btn">
        <i class="bi bi-house me-1"></i>Volver al inicio
      </a>
    </div>
  </div>
</body>
</html>
