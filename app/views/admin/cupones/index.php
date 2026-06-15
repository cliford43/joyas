<?php /* Admin: Listado de cupones */ ?>
<div class="admin-page-header">
  <h1>Cupones</h1>
  <a href="<?= url('admin/cupones/crear') ?>" class="btn btn-gold btn-sm"><i class="bi bi-plus me-1"></i>Nuevo cupón</a>
</div>
<div class="admin-card">
  <div class="table-responsive">
    <table class="table admin-table mb-0">
      <thead><tr><th>Código</th><th>Descuento</th><th>Expira</th><th>Usos</th><th>Estado</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($cupones as $c): ?>
        <tr>
          <td><code><?= e($c['codigo']) ?></code></td>
          <td><?= (float)$c['porcentaje'] ?>%</td>
          <td><?= date('d/m/Y', strtotime($c['fecha_expiracion'])) ?></td>
          <td><?= (int)$c['usos_actuales'] ?> / <?= (int)$c['limite_usos'] ?></td>
          <td><span class="badge <?= $c['activo'] ? 'bg-success' : 'bg-secondary' ?>"><?= $c['activo'] ? 'Activo' : 'Inactivo' ?></span></td>
          <td class="text-end">
            <a href="<?= url('admin/cupones/' . $c['id'] . '/editar') ?>" class="btn btn-sm btn-outline-secondary me-1">Editar</a>
            <form method="POST" action="<?= url('admin/cupones/' . $c['id'] . '/toggle') ?>" class="d-inline"
                  data-confirm="¿Cambiar estado?">
              <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
              <button class="btn btn-sm <?= $c['activo'] ? 'btn-outline-warning' : 'btn-outline-success' ?>">
                <?= $c['activo'] ? 'Desactivar' : 'Activar' ?>
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($cupones)): ?>
          <tr><td colspan="6" class="text-center text-muted py-4">Sin cupones aún.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
