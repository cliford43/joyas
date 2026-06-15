<section class="py-5" style="min-height:80vh;display:flex;align-items:center;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 text-center">
        <i class="bi bi-envelope-check" style="font-size:3rem;color:var(--color-gold);"></i>
        <h1 class="section-title mt-3">Verifica tu correo</h1>
        <p class="section-subtitle mb-4">
          Te enviamos un código de 6 dígitos a tu correo electrónico. Ingrésalo aquí para activar tu cuenta.
        </p>

        <?php if (!empty($error)): ?>
          <div class="alert-viluna-error mb-3"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= url('verificar/check') ?>" class="text-start">
          <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
          <?php if (!empty($email)): ?>
            <input type="hidden" name="email" value="<?= e($email) ?>">
          <?php endif; ?>
          <label class="form-label-viluna" for="codigo">Código de verificación</label>
          <input type="text" id="codigo" name="codigo" class="form-control form-control-viluna text-center mb-3"
                 maxlength="6" pattern="\d{6}" placeholder="000000" required
                 style="font-size:2rem;letter-spacing:8px;font-family:monospace;">
          <button type="submit" class="btn btn-gold w-100">Verificar cuenta</button>
        </form>

        <hr class="my-4" style="border-color:rgba(212,175,55,0.2);">
        <p style="font-size:0.85rem;color:var(--color-gray);">¿No recibiste el correo?</p>
        <form method="POST" action="<?= url('auth/reenviar') ?>">
          <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
          <input type="email" name="correo" placeholder="Tu correo" class="form-control form-control-viluna mb-2"
                 value="<?= e($email ?? $_SESSION['verificar_email'] ?? '') ?>">
          <button type="submit" class="btn btn-outline-gold w-100 btn-sm">Reenviar código</button>
        </form>
      </div>
    </div>
  </div>
</section>
