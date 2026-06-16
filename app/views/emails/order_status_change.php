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
      </td></tr>
      <!-- Body -->
      <tr><td style="padding:40px;">
        <h2 style="color:#111;font-family:Georgia,serif;margin:0 0 8px;">Actualización de tu pedido</h2>
        <p style="color:#D4AF37;font-size:13px;letter-spacing:2px;margin:0 0 24px;">
          ORDEN #<?= htmlspecialchars($numero_pedido ?? '', ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p style="color:#555;line-height:1.7;margin:0 0 24px;">
          Hola <?= htmlspecialchars($nombre_cliente ?? 'cliente', ENT_QUOTES, 'UTF-8') ?>,
          el estado de tu pedido ha sido actualizado.
        </p>
        <!-- New status badge -->
        <div style="background:#111;border-left:4px solid #D4AF37;padding:16px 24px;margin:20px 0;">
          <span style="color:#D4AF37;font-size:18px;font-weight:700;letter-spacing:3px;text-transform:uppercase;">
            <?= htmlspecialchars($nuevo_estado ?? '', ENT_QUOTES, 'UTF-8') ?>
          </span>
        </div>
        <!-- Details -->
        <table width="100%" cellpadding="10" style="border-collapse:collapse;margin:20px 0;">
          <tr>
            <td style="color:#555;font-size:14px;border-bottom:1px solid #eee;"><strong>Fecha de actualización:</strong></td>
            <td style="color:#111;font-size:14px;text-align:right;border-bottom:1px solid #eee;"><?= htmlspecialchars($fecha_actualizacion ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
        </table>
        <?php if (!empty($comentarios)): ?>
        <div style="background:#F8F6F0;border:1px solid rgba(212,175,55,0.2);padding:16px;margin:20px 0;">
          <p style="color:#555;font-size:13px;margin:0 0 6px;"><strong>Comentarios:</strong></p>
          <p style="color:#333;font-size:14px;line-height:1.6;margin:0;"><?= htmlspecialchars($comentarios, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php endif; ?>
        <div style="text-align:center;margin:32px 0;">
          <a href="<?= htmlspecialchars($orden_url ?? '#', ENT_QUOTES, 'UTF-8') ?>"
             style="background:#D4AF37;color:#111;padding:14px 36px;text-decoration:none;font-weight:700;font-size:13px;letter-spacing:2px;display:inline-block;">
            VER MI PEDIDO
          </a>
        </div>
      </td></tr>
      <!-- Footer -->
      <tr><td style="background:#F8F6F0;padding:20px 40px;text-align:center;border-top:1px solid rgba(212,175,55,0.2);">
        <p style="color:#aaa;font-size:12px;margin:0;">© <?= date('Y') ?> <?= htmlspecialchars($tienda_nombre ?? 'VILUNA', ENT_QUOTES, 'UTF-8') ?> Joyería</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
