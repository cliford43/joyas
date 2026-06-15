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
        <h2 style="color:#111;font-family:Georgia,serif;margin:0 0 16px;">Verifica tu cuenta</h2>
        <p style="color:#555;line-height:1.7;margin:0 0 24px;">
          Hola <?= htmlspecialchars($nombre ?? 'cliente', ENT_QUOTES, 'UTF-8') ?>, gracias por registrarte en <strong>VILUNA</strong>.
          Usa el siguiente código para activar tu cuenta:
        </p>
        <!-- Código -->
        <div style="background:#111;border:2px solid #D4AF37;padding:24px;text-align:center;margin:24px 0;">
          <div style="color:#D4AF37;font-size:42px;font-weight:700;letter-spacing:12px;font-family:monospace;">
            <?= htmlspecialchars($codigo ?? '', ENT_QUOTES, 'UTF-8') ?>
          </div>
          <div style="color:rgba(255,255,255,0.5);font-size:11px;margin-top:8px;letter-spacing:2px;">CÓDIGO DE VERIFICACIÓN</div>
        </div>
        <p style="color:#888;font-size:13px;margin:0 0 8px;">
          Este código es válido por <strong>24 horas</strong>.
        </p>
        <?php if (!empty($verificarUrl)): ?>
        <div style="text-align:center;margin:32px 0;">
          <a href="<?= htmlspecialchars($verificarUrl, ENT_QUOTES, 'UTF-8') ?>"
             style="background:#D4AF37;color:#111;padding:14px 36px;text-decoration:none;font-weight:700;font-size:13px;letter-spacing:2px;display:inline-block;">
            VERIFICAR CUENTA
          </a>
        </div>
        <?php endif; ?>
      </td></tr>
      <!-- Footer -->
      <tr><td style="background:#F8F6F0;padding:20px 40px;text-align:center;border-top:1px solid rgba(212,175,55,0.2);">
        <p style="color:#aaa;font-size:12px;margin:0;">
          © <?= date('Y') ?> VILUNA Joyería — Si no creaste esta cuenta, ignora este correo.
        </p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
