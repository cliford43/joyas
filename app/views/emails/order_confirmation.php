<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#F4F5F7;font-family:'Helvetica Neue',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#F4F5F7;padding:40px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border:1px solid rgba(212,175,55,0.3);max-width:600px;width:100%;">
      <tr><td style="background:#111111;padding:30px 40px;text-align:center;">
        <div style="color:#D4AF37;font-size:28px;font-weight:700;letter-spacing:6px;font-family:Georgia,serif;">VILUNA</div>
      </td></tr>
      <tr><td style="padding:40px;">
        <h2 style="color:#111;font-family:Georgia,serif;margin:0 0 8px;">¡Orden confirmada!</h2>
        <p style="color:#D4AF37;font-size:13px;letter-spacing:2px;margin:0 0 24px;">
          ORDEN #<?= htmlspecialchars($ordenId ?? '', ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p style="color:#555;line-height:1.7;margin:0 0 24px;">
          Hola <?= htmlspecialchars($nombre ?? 'cliente', ENT_QUOTES, 'UTF-8') ?>,
          hemos recibido tu orden. Te notificaremos cuando sea procesada.
        </p>
        <!-- Resumen -->
        <?php if (!empty($items)): ?>
        <table width="100%" cellpadding="8" style="border-collapse:collapse;margin-bottom:20px;">
          <thead>
            <tr style="background:#111;">
              <th style="color:#D4AF37;font-size:11px;letter-spacing:2px;text-align:left;">PRODUCTO</th>
              <th style="color:#D4AF37;font-size:11px;letter-spacing:2px;text-align:center;">CANT.</th>
              <th style="color:#D4AF37;font-size:11px;letter-spacing:2px;text-align:right;">PRECIO</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
            <tr style="border-bottom:1px solid #eee;">
              <td style="color:#333;"><?= htmlspecialchars($item['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td style="color:#333;text-align:center;"><?= (int)($item['cantidad'] ?? 1) ?></td>
              <td style="color:#333;text-align:right;"><?= formatPrice((float)($item['precio_unitario'] ?? 0)) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
        <div style="text-align:right;padding:12px 0;border-top:2px solid #D4AF37;">
          <strong style="font-size:18px;color:#111;">Total: <?= formatPrice((float)($total ?? 0)) ?></strong>
        </div>
        <div style="background:#F8F6F0;padding:16px;margin-top:20px;font-size:13px;color:#555;">
          <strong>Método de pago:</strong>
          <?= htmlspecialchars($metodoPago ?? '', ENT_QUOTES, 'UTF-8') ?><br>
          <strong>Dirección:</strong>
          <?= htmlspecialchars($direccion ?? '', ENT_QUOTES, 'UTF-8') ?>
        </div>
      </td></tr>
      <tr><td style="background:#F8F6F0;padding:20px 40px;text-align:center;border-top:1px solid rgba(212,175,55,0.2);">
        <p style="color:#aaa;font-size:12px;margin:0;">© <?= date('Y') ?> VILUNA Joyería. Gracias por tu compra.</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
