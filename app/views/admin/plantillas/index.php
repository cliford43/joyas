<?php /* Admin: Listado de plantillas de correo */ ?>
<div class="admin-page-header">
  <h1>Plantillas de Correo</h1>
</div>
<div class="admin-card">
  <div class="table-responsive">
    <table class="table admin-table mb-0">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Asunto</th>
          <th>Última actualización</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($plantillas as $p): ?>
        <tr>
          <td><?= e($p['nombre']) ?></td>
          <td><?= e($p['asunto']) ?></td>
          <td><?= date('d/m/Y H:i', strtotime($p['updated_at'])) ?></td>
          <td class="text-end">
            <a href="<?= url('admin/plantillas-correo/' . $p['id'] . '/editar') ?>" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-pencil me-1"></i>Editar
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($plantillas)): ?>
          <tr><td colspan="4" class="text-center text-muted py-4">No hay plantillas registradas.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
