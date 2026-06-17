<?php /* Admin: Formulario crear/editar producto */ ?>
<div class="admin-page-header">
  <h1><?= $producto ? 'Editar producto' : 'Nuevo producto' ?></h1>
  <a href="<?= url('admin/productos') ?>" class="btn btn-outline-secondary btn-sm">Volver</a>
</div>
<?php if (!empty($errors)): ?>
  <div class="alert alert-danger"><?php foreach ($errors as $e): ?><p class="mb-0"><?= e($e) ?></p><?php endforeach; ?></div>
<?php endif; ?>
<div class="admin-card admin-form">
  <form method="POST" enctype="multipart/form-data"
        action="<?= $producto ? url('admin/productos/' . $producto['id'] . '/editar') : url('admin/productos/crear') ?>">
    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label">Nombre del producto</label>
        <input type="text" name="nombre" class="form-control" required
               value="<?= e($old['nombre'] ?? $producto['nombre'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Categoría</label>
        <select name="categoria_id" class="form-select" required>
          <option value="">Seleccionar...</option>
          <?php foreach ($categorias as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>"
              <?= ((int)($old['categoria_id'] ?? $producto['categoria_id'] ?? 0)) === (int)$cat['id'] ? 'selected' : '' ?>>
              <?= e($cat['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="4"><?= e($old['descripcion'] ?? $producto['descripcion'] ?? '') ?></textarea>
      </div>
      <div class="col-md-4">
        <label class="form-label">Precio (Q)</label>
        <input type="number" name="precio" class="form-control" step="0.01" min="0" required
               value="<?= e($old['precio'] ?? $producto['precio'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Descuento (Q)</label>
        <input type="number" name="descuento" class="form-control" step="0.01" min="0"
               value="<?= e($old['descuento'] ?? $producto['descuento'] ?? '0') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control" min="0" required
               value="<?= e($old['stock'] ?? $producto['stock'] ?? '0') ?>">
      </div>
      <div class="col-md-6 d-flex gap-3 align-items-center">
        <div class="form-check">
          <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1"
                 <?= ($old['activo'] ?? $producto['activo'] ?? 1) ? 'checked' : '' ?>>
          <label class="form-check-label" for="activo">Activo</label>
        </div>
        <div class="form-check">
          <input type="checkbox" name="destacado" id="destacado" class="form-check-input" value="1"
                 <?= ($old['destacado'] ?? $producto['destacado'] ?? 0) ? 'checked' : '' ?>>
          <label class="form-check-label" for="destacado">Destacado</label>
        </div>
      </div>

      <!-- Imágenes actuales -->
      <?php if (!empty($imagenes)): ?>
      <div class="col-12">
        <label class="form-label">Imágenes actuales (<?= count($imagenes) ?>/10)</label>
        <div class="img-preview-grid">
          <?php foreach ($imagenes as $img): ?>
            <div class="img-item">
              <img src="<?= e(mediaUrl((string)$img['ruta'])) ?>" alt="imagen">
              <button class="remove-img" type="button"
                      data-delete-url="<?= url('admin/productos/' . $producto['id'] . '/imagen/eliminar') ?>"
                      data-image-id="<?= (int)$img['id'] ?>"
                      data-csrf="<?= $csrfToken ?>"
                      aria-label="Eliminar imagen">&times;</button>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

<script>
document.querySelectorAll('.remove-img[data-delete-url]').forEach(function(btn){
  btn.addEventListener('click', function(){
    if (!confirm('¿Eliminar esta imagen?')) return;
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = this.dataset.deleteUrl;
    form.innerHTML = '<input type="hidden" name="_csrf_token" value="'+this.dataset.csrf+'">'
                   + '<input type="hidden" name="image_id" value="'+this.dataset.imageId+'">';
    document.body.appendChild(form);
    form.submit();
  });
});
</script>

      <!-- Subir nuevas imágenes -->
      <?php $puedeSubir = count($imagenes ?? []) < 10; ?>
      <?php if ($puedeSubir): ?>
      <div class="col-12">
        <label class="form-label">
          Agregar imágenes (JPG/PNG, máx. 2 MB c/u, hasta <?= 10 - count($imagenes ?? []) ?> más)
        </label>
        <input type="file" name="imagenes[]" class="form-control" multiple
               accept=".jpg,.jpeg,.png" data-preview="imgPreview">
        <div id="imgPreview" class="img-preview-grid mt-2"></div>
      </div>
      <?php endif; ?>

      <div class="col-12">
        <button type="submit" class="btn btn-gold" data-loading-text="Guardando..."><?= $producto ? 'Actualizar' : 'Crear' ?></button>
      </div>
    </div>
  </form>
</div>
