<?php /* Admin: Gestión de usuarios */ ?>
<div class="admin-page-header"><h1>Usuarios</h1></div>
<div class="admin-card">
  <div class="table-responsive">
    <table class="table admin-table mb-0">
      <thead><tr><th>#</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Registro</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr>
          <td><?= (int)$u['id'] ?></td>
          <td><?= e($u['nombre'] . ' ' . $u['apellido']) ?></td>
          <td><?= e($u['correo']) ?></td>
          <td><span class="badge <?= $u['rol'] === 'admin' ? 'bg-warning text-dark' : 'bg-secondary' ?>"><?= e($u['rol']) ?></span></td>
          <td><span class="badge <?= $u['verificado'] ? 'bg-success' : 'bg-danger' ?>"><?= $u['verificado'] ? 'Activo' : 'Inactivo' ?></span></td>
          <td><?= date('d/m/Y', strtotime($u['fecha_creacion'])) ?></td>
          <td>
            <form method="POST" action="<?= url('admin/usuarios/' . $u['id'] . '/toggle') ?>" class="d-inline"
                  data-confirm="¿Cambiar estado de este usuario?">
              <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
              <button class="btn btn-sm <?= $u['verificado'] ? 'btn-outline-warning' : 'btn-outline-success' ?>">
                <?= $u['verificado'] ? 'Desactivar' : 'Activar' ?>
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
