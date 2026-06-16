<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#F4F5F7;font-family:'Helvetica Neue',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#F4F5F7;padding:40px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border:1px solid rgba(212,175,55,0.3);max-width:600px;width:100%;">
      <!-- Header -->
      <tr><td style="background:#111111;padding:30px 40px;text-align:center;">
        <div style="color:#D4AF37;font-size:28px;font-weight:700;letter-spacing:6px;font-family:Georgia,serif;">VILUNA</div>
        <div style="color:rgba(255,255,255,0.5);font-size:10px;letter-spacing:4px;margin-top:4px;">PANEL ADMINISTRATIVO</div>
      </td></tr>
      <!-- Body -->
      <tr><td style="padding:40px;">
        <h2 style="color:#111;font-family:Georgia,serif;margin:0 0 16px;">
          <?= htmlspecialchars($titulo ?? 'Notificación del sistema', ENT_QUOTES, 'UTF-8') ?>
        </h2>
        <p style="color:#555;line-height:1.7;margin:0 0 24px;font-size:15px;">
          <?= htmlspecialchars($mensaje ?? '', ENT_QUOTES, 'UTF-8') ?>
        </p>
        <?php if (!empty($nombre_cliente)): ?>
        <table width="100%" cellpadding="10" style="border-collapse:collapse;background:#F8F6F0;border:1px solid rgba(212,175,55,0.2);margin-bottom:24px;">
          <tr>
            <td style="color:#555;font-size:13px;border-bottom:1px solid rgba(212,175,55,0.15);"><strong>Cliente:</strong></td>
            <td style="color:#111;font-size:13px;text-align:right;border-bottom:1px solid rgba(212,175,55,0.15);"><?= htmlspecialchars($nombre_cliente ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <?php if (!empty($correo_cliente)): ?>
          <tr>
            <td style="color:#555;font-size:13px;border-bottom:1px solid rgba(212,175,55,0.15);"><strong>Correo:</strong></td>
            <td style="color:#111;font-size:13px;text-align:right;border-bottom:1px solid rgba(212,175,55,0.15);"><?= htmlspecialchars($correo_cliente ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <?php endif; ?>
          <?php if (!empty($numero_pedido)): ?>
          <tr>
            <td style="color:#555;font-size:13px;border-bottom:1px solid rgba(212,175,55,0.15);"><strong>Pedido:</strong></td>
            <td style="color:#111;font-size:13px;text-align:right;border-bottom:1px solid rgba(212,175,55,0.15);">#<?= htmlspecialchars($numero_pedido ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <?php endif; ?>
          <?php if (!empty($total)): ?>
          <tr>
            <td style="color:#555;font-size:13px;border-bottom:1px solid rgba(212,175,55,0.15);"><strong>Total:</strong></td>
            <td style="color:#111;font-size:13px;text-align:right;border-bottom:1px solid rgba(212,175,55,0.15);">Q <?= htmlspecialchars($total ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <?php endif; ?>
          <?php if (!empty($producto_nombre)): ?>
          <tr>
            <td style="color:#555;font-size:13px;border-bottom:1px solid rgba(212,175,55,0.15);"><strong>Producto:</strong></td>
            <td style="color:#111;font-size:13px;text-align:right;border-bottom:1px solid rgba(212,175,55,0.15);"><?= htmlspecialchars($producto_nombre ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <?php endif; ?>
          <?php if (!empty($calificacion)): ?>
          <tr>
            <td style="color:#555;font-size:13px;border-bottom:1px solid rgba(212,175,55,0.15);"><strong>Calificación:</strong></td>
            <td style="color:#111;font-size:13px;text-align:right;border-bottom:1px solid rgba(212,175,55,0.15);"><?= str_repeat('★', (int)$calificacion) . str_repeat('☆', 5 - (int)$calificacion) ?></td>
          </tr>
          <?php endif; ?>
          <tr>
            <td style="color:#555;font-size:13px;"><strong>Fecha:</strong></td>
            <td style="color:#111;font-size:13px;text-align:right;"><?= htmlspecialchars($fecha ?? date('Y-m-d H:i:s'), ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
        </table>
        <?php endif; ?>
        <?php if (!empty($comentario)): ?>
        <div style="background:#F8F6F0;border-left:4px solid #D4AF37;padding:12px 16px;margin-bottom:24px;">
          <p style="color:#555;font-size:12px;margin:0 0 4px;"><strong>Comentario del cliente:</strong></p>
          <p style="color:#333;font-size:14px;line-height:1.5;margin:0;"><?= htmlspecialchars($comentario, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php endif; ?>
        <p style="color:#888;font-size:13px;margin:0;">
          Este es un correo automático del sistema <?= htmlspecialchars($tienda_nombre ?? 'VILUNA', ENT_QUOTES, 'UTF-8') ?>.
        </p>
      </td></tr>
      <!-- Footer -->
      <tr><td style="background:#F8F6F0;padding:20px 40px;text-align:center;border-top:1px solid rgba(212,175,55,0.2);">
        <p style="color:#aaa;font-size:12px;margin:0;">© <?= date('Y') ?> <?= htmlspecialchars($tienda_nombre ?? 'VILUNA', ENT_QUOTES, 'UTF-8') ?> Joyería — Notificación administrativa</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
