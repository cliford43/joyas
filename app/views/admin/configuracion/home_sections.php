<?php /* Admin: Configuración de secciones del Home */ ?>
<div class="admin-page-header">
  <h1>Secciones de la página principal</h1>
  <p class="text-muted">Configura las 4 secciones de productos que se muestran en la página de inicio.</p>
</div>

<div class="admin-card admin-form" style="max-width:900px;">
  <form method="POST" action="<?= url('admin/configuracion/home') ?>">
    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

    <?php for ($i = 1; $i <= 4; $i++): $sec = $sections[$i]; ?>
    <div class="border rounded p-3 mb-4" style="border-color:rgba(212,175,55,0.3) !important;">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h6 text-uppercase mb-0" style="color:var(--color-gold);letter-spacing:2px;">
          Sección <?= $i ?>
        </h2>
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="sec<?= $i ?>_activo"
                 name="sec<?= $i ?>_activo" value="1" <?= ($sec['activo'] ?? '1') === '1' ? 'checked' : '' ?>>
          <label class="form-check-label" for="sec<?= $i ?>_activo">Activa</label>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Título</label>
          <input type="text" name="sec<?= $i ?>_titulo" class="form-control"
                 value="<?= e($sec['titulo'] ?? '') ?>" placeholder="Ej: Piezas Destacadas">
        </div>
        <div class="col-md-6">
          <label class="form-label">Descripción / Subtítulo</label>
          <input type="text" name="sec<?= $i ?>_descripcion" class="form-control"
                 value="<?= e($sec['descripcion'] ?? '') ?>" placeholder="Ej: Selección especial de nuestros artesanos">
        </div>
        <div class="col-md-4">
          <label class="form-label">Tipo de contenido</label>
          <select name="sec<?= $i ?>_tipo" class="form-select">
            <?php foreach ($tipos as $key => $label): ?>
              <option value="<?= $key ?>" <?= ($sec['tipo'] ?? '') === $key ? 'selected' : '' ?>>
                <?= e($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Cantidad de productos</label>
          <input type="number" name="sec<?= $i ?>_cantidad" class="form-control"
                 min="1" max="24" value="<?= (int)($sec['cantidad'] ?? 8) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Orden</label>
          <select name="sec<?= $i ?>_orden" class="form-select">
            <option value="recientes" <?= ($sec['orden'] ?? '') === 'recientes' ? 'selected' : '' ?>>Más recientes</option>
            <option value="antiguos" <?= ($sec['orden'] ?? '') === 'antiguos' ? 'selected' : '' ?>>Más antiguos</option>
            <option value="precio_asc" <?= ($sec['orden'] ?? '') === 'precio_asc' ? 'selected' : '' ?>>Precio menor a mayor</option>
            <option value="precio_desc" <?= ($sec['orden'] ?? '') === 'precio_desc' ? 'selected' : '' ?>>Precio mayor a menor</option>
            <option value="aleatorio" <?= ($sec['orden'] ?? '') === 'aleatorio' ? 'selected' : '' ?>>Aleatorio</option>
          </select>
        </div>
      </div>
    </div>
    <?php endfor; ?>

    <button type="submit" class="btn btn-gold" data-loading-text="Guardando...">Guardar secciones</button>
    <button type="submit" name="reset_sections" value="1" class="btn btn-outline-secondary ms-2"
            onclick="return confirm('¿Restablecer todas las secciones a los valores por defecto?');">
      Restablecer secciones
    </button>
    <a href="<?= url('admin/configuracion') ?>" class="btn btn-outline-secondary ms-2">Volver a configuración</a>
  </form>
</div>
