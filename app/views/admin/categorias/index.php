<?php /* Admin: Listado de categorías */ ?>
<div class="admin-page-header">
  <h1>Categorías</h1>
  <a href="<?= url('admin/categorias/crear') ?>" class="btn btn-gold btn-sm">
    <i class="bi bi-plus me-1"></i>Nueva categoría
  </a>
</div>
<div class="admin-card">
  <div class="table-responsive">
    <table class="table admin-table mb-0">
      <thead><tr><th>#</th><th>Imagen</th><th>Nombre</th><th>Slug</th><th>Estado</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($categorias as $cat): ?>
        <tr>
          <td><?= (int)$cat['id'] ?></td>
          <td>
            <?php if (!empty($cat['imagen'])): ?>
              <img src="<?= e(mediaUrl((string)$cat['imagen'])) ?>" alt="<?= e($cat['nombre']) ?>"
                   style="width:72px;height:48px;object-fit:cover;border-radius:10px;">
            <?php else: ?>
              <span class="text-muted small">Sin imagen</span>
            <?php endif; ?>
          </td>
          <td><?= e($cat['nombre']) ?></td>
          <td><code><?= e($cat['slug']) ?></code></td>
          <td>
            <span class="badge <?= $cat['activo'] ? 'bg-success' : 'bg-secondary' ?>">
              <?= $cat['activo'] ? 'Activo' : 'Inactivo' ?>
            </span>
          </td>
          <td class="text-end">
            <a href="<?= url('admin/categorias/' . $cat['id'] . '/editar') ?>" class="btn btn-sm btn-outline-secondary me-1">Editar</a>
            <form method="POST" action="<?= url('admin/categorias/' . $cat['id'] . '/toggle') ?>" class="d-inline"
                  data-confirm="¿Cambiar estado de esta categoría?">
              <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
              <button class="btn btn-sm <?= $cat['activo'] ? 'btn-outline-warning' : 'btn-outline-success' ?>">
                <?= $cat['activo'] ? 'Desactivar' : 'Activar' ?>
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
