<?php /* Admin: Listado de productos */ ?>
<div class="admin-page-header">
  <h1>Productos</h1>
  <a href="<?= url('admin/productos/crear') ?>" class="btn btn-gold btn-sm">
    <i class="bi bi-plus me-1"></i>Nuevo producto
  </a>
</div>
<div class="admin-card">
  <div class="table-responsive">
    <table class="table admin-table mb-0">
      <thead><tr><th>#</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Estado</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($productos as $p): ?>
        <tr>
          <td><?= (int)$p['id'] ?></td>
          <td><?= e($p['nombre']) ?></td>
          <td><?= e($p['categoria_nombre']) ?></td>
          <td><?= formatPrice((float)$p['precio']) ?></td>
          <td><?= (int)$p['stock'] ?></td>
          <td>
            <span class="badge <?= $p['activo'] ? 'bg-success' : 'bg-secondary' ?>">
              <?= $p['activo'] ? 'Activo' : 'Inactivo' ?>
            </span>
            <?php if ($p['destacado']): ?><span class="badge" style="background:#D4AF37;color:#111;">Destacado</span><?php endif; ?>
          </td>
          <td class="text-end">
            <a href="<?= url('admin/productos/' . $p['id'] . '/editar') ?>" class="btn btn-sm btn-outline-secondary me-1">Editar</a>
            <form method="POST" action="<?= url('admin/productos/' . $p['id'] . '/toggle') ?>" class="d-inline"
                  data-confirm="¿Cambiar estado?">
              <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
              <button class="btn btn-sm <?= $p['activo'] ? 'btn-outline-warning' : 'btn-outline-success' ?>">
                <?= $p['activo'] ? 'Desactivar' : 'Activar' ?>
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
