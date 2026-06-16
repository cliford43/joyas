<?php /* Admin: Bitácora de correos enviados */ ?>
<div class="admin-page-header">
  <h1>Bitácora de Correos</h1>
  <span class="text-muted"><?= $total ?> registros</span>
</div>
<div class="admin-card">
  <div class="table-responsive">
    <table class="table admin-table mb-0">
      <thead>
        <tr>
          <th>Destinatario</th>
          <th>Asunto</th>
          <th>Fecha</th>
          <th>Estado</th>
          <th>Error</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($correos as $c): ?>
        <tr>
          <td><?= e($c['destinatario']) ?></td>
          <td><?= e(truncate($c['asunto'], 60)) ?></td>
          <td><?= date('d/m/Y H:i', strtotime($c['fecha_envio'])) ?></td>
          <td>
            <span class="badge <?= $c['estado'] === 'enviado' ? 'bg-success' : 'bg-danger' ?>">
              <?= e($c['estado']) ?>
            </span>
          </td>
          <td>
            <?php if ($c['error_mensaje']): ?>
              <small class="text-danger"><?= e(truncate($c['error_mensaje'], 80)) ?></small>
            <?php else: ?>
              <span class="text-muted">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($correos)): ?>
          <tr><td colspan="5" class="text-center text-muted py-4">No hay correos registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($totalPages > 1): ?>
  <nav class="mt-3" aria-label="Paginación de correos">
    <ul class="pagination pagination-sm justify-content-center mb-0">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
          <a class="page-link" href="<?= url('admin/correos-log?page=' . $i) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <?php endif; ?>
</div>
