<?php /* Vista: Editar perfil del cliente */ ?>
<div class="container py-5">
  <div class="row g-4">
    <div class="col-lg-3"><?php include APP_PATH . '/views/client/partials/sidebar.php'; ?></div>
    <div class="col-lg-9">
      <h1 class="font-heading mb-4">Mi perfil</h1>

      <?php if (!empty($errors)): ?>
        <div class="alert-viluna-error mb-3">
          <?php foreach ($errors as $e): ?><p class="mb-0"><?= e($e) ?></p><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="admin-card">
        <form method="POST" action="<?= url('mi-cuenta/perfil') ?>">
          <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label-viluna" for="nombre">Nombre</label>
              <input type="text" id="nombre" name="nombre" class="form-control form-control-viluna"
                     value="<?= e($old['nombre'] ?? $user['nombre'] ?? '') ?>" required>
            </div>
            <div class="col-sm-6">
              <label class="form-label-viluna" for="apellido">Apellido</label>
              <input type="text" id="apellido" name="apellido" class="form-control form-control-viluna"
                     value="<?= e($old['apellido'] ?? $user['apellido'] ?? '') ?>" required>
            </div>
            <div class="col-sm-6">
              <label class="form-label-viluna" for="correo">Correo electrónico</label>
              <input type="email" id="correo" class="form-control form-control-viluna"
                     value="<?= e($user['correo'] ?? '') ?>" disabled>
              <div class="form-text">El correo no puede cambiarse.</div>
            </div>
            <div class="col-sm-6">
              <label class="form-label-viluna" for="telefono">Teléfono</label>
              <input type="tel" id="telefono" name="telefono" class="form-control form-control-viluna"
                     value="<?= e($old['telefono'] ?? $user['telefono'] ?? '') ?>">
            </div>
            <div class="col-12">
              <label class="form-label-viluna" for="direccion">Dirección</label>
              <textarea id="direccion" name="direccion" rows="3" class="form-control form-control-viluna"><?= e($old['direccion'] ?? $user['direccion'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-gold">Guardar cambios</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
