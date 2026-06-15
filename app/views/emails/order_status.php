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
        <h2 style="color:#111;font-family:Georgia,serif;margin:0 0 8px;">Actualización de tu orden</h2>
        <p style="color:#D4AF37;font-size:13px;letter-spacing:2px;margin:0 0 24px;">
          ORDEN #<?= htmlspecialchars($ordenId ?? '', ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p style="color:#555;line-height:1.7;margin:0 0 24px;">
          Hola <?= htmlspecialchars($nombre ?? 'cliente', ENT_QUOTES, 'UTF-8') ?>,
          el estado de tu orden ha cambiado a:
        </p>
        <div style="background:#111;border-left:4px solid #D4AF37;padding:16px 24px;margin:20px 0;">
          <span style="color:#D4AF37;font-size:18px;font-weight:700;letter-spacing:3px;text-transform:uppercase;">
            <?= htmlspecialchars($estado ?? '', ENT_QUOTES, 'UTF-8') ?>
          </span>
        </div>
        <p style="color:#888;font-size:13px;">
          Puedes revisar el detalle de tu orden en
          <a href="<?= htmlspecialchars($ordenUrl ?? '#', ENT_QUOTES, 'UTF-8') ?>" style="color:#D4AF37;">
            tu panel de cliente
          </a>.
        </p>
      </td></tr>
      <tr><td style="background:#F8F6F0;padding:20px 40px;text-align:center;border-top:1px solid rgba(212,175,55,0.2);">
        <p style="color:#aaa;font-size:12px;margin:0;">© <?= date('Y') ?> VILUNA Joyería</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
