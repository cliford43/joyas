<?php /* Vista: Cambiar contraseña del cliente */ ?>
<div class="container py-5">
  <div class="row g-4">
    <div class="col-lg-3"><?php include APP_PATH . '/views/client/partials/sidebar.php'; ?></div>
    <div class="col-lg-9">
      <h1 class="font-heading mb-4">Cambiar contraseña</h1>

      <?php if (!empty($errors)): ?>
        <div class="alert-viluna-error mb-3">
          <?php foreach ($errors as $e): ?><p class="mb-0"><?= e($e) ?></p><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="admin-card" style="max-width:480px;">
        <form method="POST" action="<?= url('mi-cuenta/contrasena') ?>">
          <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
          <div class="mb-3">
            <label class="form-label-viluna" for="password_actual">Contraseña actual</label>
            <input type="password" id="password_actual" name="password_actual"
                   class="form-control form-control-viluna" required autocomplete="current-password">
          </div>
          <div class="mb-3">
            <label class="form-label-viluna" for="password_nueva">Nueva contraseña</label>
            <input type="password" id="password_nueva" name="password_nueva"
                   class="form-control form-control-viluna" required minlength="8" autocomplete="new-password">
            <div class="form-text">Mínimo 8 caracteres.</div>
          </div>
          <div class="mb-3">
            <label class="form-label-viluna" for="password_confirmar">Confirmar nueva contraseña</label>
            <input type="password" id="password_confirmar" name="password_confirmar"
                   class="form-control form-control-viluna" required autocomplete="new-password">
          </div>
          <button type="submit" class="btn btn-gold">Actualizar contraseña</button>
        </form>
      </div>
    </div>
  </div>
</div>
