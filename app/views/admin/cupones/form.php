<?php /* Admin: Formulario crear/editar cupón */ ?>
<div class="admin-page-header">
  <h1><?= $cupon ? 'Editar cupón' : 'Nuevo cupón' ?></h1>
  <a href="<?= url('admin/cupones') ?>" class="btn btn-outline-secondary btn-sm">Volver</a>
</div>
<?php if (!empty($errors)): ?>
  <div class="alert alert-danger"><?php foreach ($errors as $e): ?><p class="mb-0"><?= e($e) ?></p><?php endforeach; ?></div>
<?php endif; ?>
<div class="admin-card admin-form" style="max-width:480px;">
  <form method="POST" action="<?= $cupon ? url('admin/cupones/' . $cupon['id'] . '/editar') : url('admin/cupones/crear') ?>">
    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
    <div class="mb-3">
      <label class="form-label">Código</label>
      <input type="text" name="codigo" class="form-control text-uppercase" required
             value="<?= e($old['codigo'] ?? $cupon['codigo'] ?? '') ?>"
             style="text-transform:uppercase;">
    </div>
    <div class="mb-3">
      <label class="form-label">Porcentaje de descuento (%)</label>
      <input type="number" name="porcentaje" class="form-control" step="0.01" min="1" max="100" required
             value="<?= e($old['porcentaje'] ?? $cupon['porcentaje'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Fecha de expiración</label>
      <input type="datetime-local" name="fecha_expiracion" class="form-control" required
             value="<?= e($old['fecha_expiracion'] ?? (isset($cupon['fecha_expiracion']) ? date('Y-m-d\TH:i', strtotime($cupon['fecha_expiracion'])) : '')) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Límite de usos</label>
      <input type="number" name="limite_usos" class="form-control" min="1" required
             value="<?= e($old['limite_usos'] ?? $cupon['limite_usos'] ?? '100') ?>">
    </div>
    <div class="mb-3 form-check">
      <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1"
             <?= ($old['activo'] ?? $cupon['activo'] ?? 1) ? 'checked' : '' ?>>
      <label class="form-check-label" for="activo">Activo</label>
    </div>
    <button type="submit" class="btn btn-gold"><?= $cupon ? 'Actualizar' : 'Crear cupón' ?></button>
  </form>
</div>
