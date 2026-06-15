<?php /* Admin: Gestión de órdenes */ ?>
<div class="admin-page-header">
  <h1>Órdenes</h1>
</div>
<!-- Filtro por estado -->
<div class="admin-card mb-3">
  <div class="d-flex gap-2 flex-wrap">
    <a href="<?= url('admin/ordenes') ?>" class="btn btn-sm <?= empty($estadoFiltro) ? 'btn-gold' : 'btn-outline-secondary' ?>">Todas</a>
    <?php foreach ($estados as $k => $v): ?>
      <a href="<?= url('admin/ordenes?estado=' . $k) ?>"
         class="btn btn-sm <?= $estadoFiltro === $k ? 'btn-gold' : 'btn-outline-secondary' ?>">
        <?= e($v) ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<div class="admin-card">
  <div class="table-responsive">
    <table class="table admin-table mb-0">
      <thead><tr><th>#</th><th>Cliente</th><th>Estado</th><th>Método</th><th>Fecha</th><th class="text-end">Total</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($ordenes as $o): ?>
        <tr>
          <td>#<?= (int)$o['id'] ?></td>
          <td><?= e($o['nombre'] . ' ' . $o['apellido']) ?></td>
          <td><span class="status-badge status-<?= e($o['estado']) ?>"><?= e($estados[$o['estado']] ?? '') ?></span></td>
          <td><?= $o['metodo_pago'] === 'transferencia' ? 'Transferencia' : 'Contra entrega' ?></td>
          <td><?= date('d/m/Y', strtotime($o['fecha_creacion'])) ?></td>
          <td class="text-end"><?= formatPrice((float)$o['total']) ?></td>
          <td><a href="<?= url('admin/ordenes/' . $o['id']) ?>" class="btn btn-sm btn-outline-secondary">Ver</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($ordenes)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No hay órdenes con este filtro.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
