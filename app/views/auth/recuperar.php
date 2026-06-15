<section class="py-5" style="min-height:80vh;display:flex;align-items:center;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 col-lg-4">
        <div class="text-center mb-4">
          <h1 class="section-title">Recuperar contraseña</h1>
          <p class="section-subtitle">Te enviaremos un enlace para restablecer tu contraseña.</p>
        </div>

        <?php if (!empty($success)): ?>
          <div class="alert-viluna-success mb-3"><?= e($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
          <div class="alert-viluna-error mb-3">
            <?php foreach ($errors as $e): ?><p class="mb-0"><?= e($e) ?></p><?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('recuperar') ?>">
          <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
          <div class="mb-3">
            <label class="form-label-viluna" for="correo">Correo electrónico</label>
            <input type="email" id="correo" name="correo" class="form-control form-control-viluna" required autocomplete="email">
          </div>
          <button type="submit" class="btn btn-gold w-100">Enviar enlace</button>
        </form>

        <p class="text-center mt-3" style="font-size:0.9rem;">
          <a href="<?= url('login') ?>" class="text-gold">Volver al login</a>
        </p>
      </div>
    </div>
  </div>
</section>
