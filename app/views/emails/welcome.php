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
        <div style="color:rgba(212,175,55,0.6);font-size:10px;letter-spacing:4px;margin-top:4px;">JOYERÍA FINA</div>
      </td></tr>
      <!-- Body -->
      <tr><td style="padding:40px;">
        <h2 style="color:#111;font-family:Georgia,serif;margin:0 0 16px;">¡Bienvenido a VILUNA!</h2>
        <p style="color:#555;line-height:1.7;margin:0 0 24px;">
          Hola <?= htmlspecialchars(($nombre_cliente ?? '') . ' ' . ($apellido ?? ''), ENT_QUOTES, 'UTF-8') ?>,
          gracias por registrarte en <strong><?= htmlspecialchars($tienda_nombre ?? 'VILUNA', ENT_QUOTES, 'UTF-8') ?></strong>.
          Tu cuenta ha sido creada exitosamente.
        </p>
        <div style="background:#F8F6F0;border:1px solid rgba(212,175,55,0.2);padding:20px;margin:0 0 24px;">
          <p style="color:#555;font-size:14px;margin:0 0 8px;"><strong>Tu correo registrado:</strong></p>
          <p style="color:#111;font-size:16px;margin:0;"><?= htmlspecialchars($correo ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <p style="color:#555;line-height:1.7;margin:0 0 24px;">
          Ya puedes explorar nuestro catálogo, agregar productos a tu lista de deseos y realizar compras.
        </p>
        <div style="text-align:center;margin:32px 0;">
          <a href="<?= htmlspecialchars($login_url ?? '#', ENT_QUOTES, 'UTF-8') ?>"
             style="background:#D4AF37;color:#111;padding:14px 36px;text-decoration:none;font-weight:700;font-size:13px;letter-spacing:2px;display:inline-block;">
            INICIAR SESIÓN
          </a>
        </div>
        <div style="border-top:1px solid rgba(212,175,55,0.2);padding-top:20px;margin-top:20px;">
          <p style="color:#888;font-size:13px;margin:0 0 4px;">¿Necesitas ayuda? Contáctanos:</p>
          <p style="color:#555;font-size:13px;margin:0;">
            <a href="<?= htmlspecialchars($tienda_url ?? '#', ENT_QUOTES, 'UTF-8') ?>" style="color:#D4AF37;text-decoration:none;">
              <?= htmlspecialchars($tienda_url ?? '', ENT_QUOTES, 'UTF-8') ?>
            </a>
          </p>
        </div>
      </td></tr>
      <!-- Footer -->
      <tr><td style="background:#F8F6F0;padding:20px 40px;text-align:center;border-top:1px solid rgba(212,175,55,0.2);">
        <p style="color:#aaa;font-size:12px;margin:0;">
          © <?= date('Y') ?> <?= htmlspecialchars($tienda_nombre ?? 'VILUNA', ENT_QUOTES, 'UTF-8') ?> Joyería — Gracias por unirte.
        </p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
