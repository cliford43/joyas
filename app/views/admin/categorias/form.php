<?php /* Admin: Formulario crear/editar categoría */ ?>
<div class="admin-page-header">
  <h1><?= $categoria ? 'Editar categoría' : 'Nueva categoría' ?></h1>
  <a href="<?= url('admin/categorias') ?>" class="btn btn-outline-secondary btn-sm">Volver</a>
</div>
<?php if (!empty($errors)): ?>
  <div class="alert alert-danger"><?php foreach ($errors as $e): ?><p class="mb-0"><?= e($e) ?></p><?php endforeach; ?></div>
<?php endif; ?>
<div class="admin-card admin-form" style="max-width:560px;">
  <form method="POST" enctype="multipart/form-data" action="<?= $categoria ? url('admin/categorias/' . $categoria['id'] . '/editar') : url('admin/categorias/crear') ?>">
    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
    <div class="mb-3">
      <label class="form-label">Nombre</label>
      <input type="text" name="nombre" class="form-control" required
             value="<?= e($old['nombre'] ?? $categoria['nombre'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Descripción</label>
      <textarea name="descripcion" class="form-control" rows="3"><?= e($old['descripcion'] ?? $categoria['descripcion'] ?? '') ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Imagen de categoría</label>
      <input type="file" name="imagen" class="form-control" accept=".jpg,.jpeg,.png">
      <div class="form-text">JPG o PNG, máximo 2 MB.</div>
      <?php if (!empty($categoria['imagen'])): ?>
        <div class="mt-3">
          <img src="<?= e(mediaUrl((string)$categoria['imagen'])) ?>" alt="<?= e($categoria['nombre'] ?? 'Categoría') ?>"
               style="width:180px;height:120px;object-fit:cover;border-radius:12px;border:1px solid rgba(0,0,0,.1);">
        </div>
      <?php endif; ?>
    </div>
    <div class="mb-3 form-check">
      <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1"
             <?= (!isset($old) && isset($categoria) ? $categoria['activo'] : ($old['activo'] ?? 1)) ? 'checked' : '' ?>>
      <label class="form-check-label" for="activo">Activo</label>
    </div>
    <button type="submit" class="btn btn-gold"><?= $categoria ? 'Actualizar' : 'Crear' ?></button>
  </form>
</div>
