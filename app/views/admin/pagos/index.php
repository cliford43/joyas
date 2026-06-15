<?php /* Admin: Panel de pagos por transferencia */ ?>
<div class="admin-page-header">
  <h1>Pagos por transferencia</h1>
</div>
<div class="admin-card">
  <?php if (empty($comprobantes)): ?>
    <p class="text-muted text-center py-4">No hay comprobantes pendientes de revisión.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table admin-table mb-0">
        <thead><tr><th>#Orden</th><th>Cliente</th><th>Total</th><th>Fecha</th><th>Comprobante</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($comprobantes as $c): ?>
          <tr>
            <td>#<?= (int)$c['id'] ?></td>
            <td><?= e($c['nombre'] . ' ' . $c['apellido']) ?><br><small class="text-muted"><?= e($c['correo']) ?></small></td>
            <td>S/ <?= number_format((float)$c['total'], 2) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($c['fecha_creacion'])) ?></td>
            <td>
              <?php if (str_ends_with(strtolower($c['comprobante_ruta']), '.pdf')): ?>
                <a href="/<?= e(ltrim($c['comprobante_ruta'], '/')) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                  <i class="bi bi-file-pdf"></i> PDF
                </a>
              <?php else: ?>
                <a href="/<?= e(ltrim($c['comprobante_ruta'], '/')) ?>" target="_blank">
                  <img src="/<?= e(ltrim($c['comprobante_ruta'], '/')) ?>" style="width:60px;height:45px;object-fit:cover;">
                </a>
              <?php endif; ?>
            </td>
            <td>
              <form method="POST" action="<?= url('admin/pagos/aprobar') ?>" class="d-inline">
                <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="orden_id" value="<?= (int)$c['id'] ?>">
                <button class="btn btn-sm btn-success me-1" data-confirm="¿Aprobar este comprobante?">Aprobar</button>
              </form>
              <form method="POST" action="<?= url('admin/pagos/rechazar') ?>" class="d-inline">
                <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="orden_id" value="<?= (int)$c['id'] ?>">
                <button class="btn btn-sm btn-danger" data-confirm="¿Rechazar y cancelar esta orden?">Rechazar</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
