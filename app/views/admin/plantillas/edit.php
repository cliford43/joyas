<?php /* Admin: Editar plantilla de correo */ ?>
<div class="admin-page-header">
  <h1>Editar Plantilla: <?= e($plantilla['nombre']) ?></h1>
  <a href="<?= url('admin/plantillas-correo') ?>" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Volver
  </a>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $err): ?>
        <li><?= e($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-lg-8">
    <div class="admin-card">
      <form method="POST" action="<?= url('admin/plantillas-correo/' . $plantilla['id'] . '/editar') ?>">
        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

        <div class="mb-3">
          <label for="asunto" class="form-label">Asunto</label>
          <input type="text" class="form-control" id="asunto" name="asunto"
                 value="<?= e($plantilla['asunto']) ?>" required>
          <small class="text-muted">Puedes usar variables dinámicas en el asunto.</small>
        </div>

        <div class="mb-3">
          <label for="contenido" class="form-label">Contenido HTML</label>
          <textarea class="form-control font-monospace" id="contenido" name="contenido"
                    rows="18" required><?= e($plantilla['contenido']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-gold">
          <i class="bi bi-check-lg me-1"></i>Guardar cambios
        </button>
      </form>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="admin-card">
      <h6 class="mb-3"><i class="bi bi-braces me-1"></i>Variables disponibles</h6>
      <p class="text-muted small">Usa estas variables en el asunto o contenido. Se reemplazarán automáticamente al enviar.</p>
      <?php if (!empty($variables)): ?>
        <ul class="list-unstyled mb-0">
          <?php foreach ($variables as $var): ?>
            <li class="mb-2">
              <code class="user-select-all">{<?= e($var) ?>}</code>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted small mb-0">Esta plantilla no tiene variables definidas.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
