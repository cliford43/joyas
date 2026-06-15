<section class="py-5" style="min-height:80vh;display:flex;align-items:center;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 col-lg-4">
        <div class="text-center mb-4">
          <h1 class="section-title">Nueva contraseña</h1>
        </div>

        <?php if (!empty($errors)): ?>
          <div class="alert-viluna-error mb-3">
            <?php foreach ($errors as $e): ?><p class="mb-0"><?= e($e) ?></p><?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('restablecer/' . ($token ?? '')) ?>">
          <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
          <div class="mb-3">
            <label class="form-label-viluna" for="password">Nueva contraseña</label>
            <input type="password" id="password" name="password" class="form-control form-control-viluna"
                   required minlength="8" autocomplete="new-password">
          </div>
          <div class="mb-3">
            <label class="form-label-viluna" for="password2">Confirmar nueva contraseña</label>
            <input type="password" id="password2" name="password2" class="form-control form-control-viluna"
                   required autocomplete="new-password">
          </div>
          <button type="submit" class="btn btn-gold w-100">Actualizar contraseña</button>
        </form>
      </div>
    </div>
  </div>
</section>
