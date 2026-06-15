<?php /* Admin: Suscriptores newsletter */ ?>
<div class="admin-page-header">
  <h1>Newsletter</h1>
  <span class="small text-muted"><?= count($suscriptores) ?> suscriptores</span>
</div>
<div class="admin-card">
  <div class="table-responsive">
    <table class="table admin-table mb-0">
      <thead><tr><th>Correo</th><th>Fecha suscripción</th></tr></thead>
      <tbody>
        <?php foreach ($suscriptores as $s): ?>
        <tr>
          <td><?= e($s['correo']) ?></td>
          <td><?= date('d/m/Y H:i', strtotime($s['fecha_suscripcion'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($suscriptores)): ?>
          <tr><td colspan="2" class="text-center text-muted py-4">Sin suscriptores aún.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
