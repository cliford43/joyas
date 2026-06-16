<?php
/**
 * Partial: Botón flotante de WhatsApp
 */
$config = $_SESSION['config'] ?? [];
$phone   = $config['whatsapp']         ?? '';
$mensaje = $config['whatsapp_mensaje'] ?? 'Hola, me interesa conocer más sobre sus joyas.';
if (empty($phone)) return;

$requestUri = (string)($_SERVER['REQUEST_URI'] ?? '');
$requestPath = trim((string)parse_url($requestUri, PHP_URL_PATH), '/');
$isProductPage = preg_match('#^producto/[^/]+/?$#', $requestPath) === 1;

if ($isProductPage && $requestUri !== '') {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = (string)($_SERVER['HTTP_HOST'] ?? '');

  if ($host !== '') {
    $currentUrl = $scheme . '://' . $host . $requestUri;
  } else {
    $currentUrl = rtrim((string)(defined('APP_URL') ? APP_URL : ''), '/') . $requestUri;
  }

  if ($currentUrl !== '') {
    $mensaje .= "\nMe interesa este producto: " . $currentUrl;
  }
}

$waUrl = 'https://wa.me/' . preg_replace('/\D/', '', $phone) . '?text=' . rawurlencode($mensaje);
?>
<a href="<?= e($waUrl) ?>" class="whatsapp-float" target="_blank" rel="noopener"
   aria-label="Contactar por WhatsApp">
  <i class="bi bi-whatsapp"></i>
  <span class="whatsapp-tooltip">¿Necesitas ayuda?</span>
</a>
