<?php
/**
 * Partial: Botón flotante de WhatsApp
 */
$config = $_SESSION['config'] ?? [];
$phone   = $config['whatsapp']         ?? '';
$mensaje = $config['whatsapp_mensaje'] ?? 'Hola, me interesa conocer más sobre sus joyas.';
if (empty($phone)) return;
$waUrl = 'https://wa.me/' . preg_replace('/\D/', '', $phone) . '?text=' . rawurlencode($mensaje);
?>
<a href="<?= e($waUrl) ?>" class="whatsapp-float" target="_blank" rel="noopener"
   aria-label="Contactar por WhatsApp">
  <i class="bi bi-whatsapp"></i>
  <span class="whatsapp-tooltip">¿Necesitas ayuda?</span>
</a>
