<?php
/* Vista: Dashboard principal del cliente */
$ordenesPendientes = array_filter($ordenes ?? [], fn($o) => $o['estado'] === 'pendiente');
?>

<div class="container py-5">
  <div class="row g-4">

    <!-- Sidebar -->
    <div class="col-lg-3">
      <?php include APP_PATH . '/views/client/partials/sidebar.php'; ?>
    </div>

    <!-- Contenido -->
    <div class="col-lg-9">
      <h1 class="font-heading mb-1">Bienvenido, <?= e($_SESSION['user_nombre'] ?? 'Cliente') ?></h1>
      <p class="text-muted mb-4">Gestiona tu cuenta, órdenes y lista de deseos.</p>

      <div class="row g-3 mb-4">
        <div class="col-sm-4">
          <div class="stat-card text-center p-3">
            <div class="stat-number"><?= count($ordenes ?? []) ?></div>
            <div class="stat-label">Órdenes totales</div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="stat-card text-center p-3">
            <div class="stat-number"><?= count($ordenesPendientes) ?></div>
            <div class="stat-label">Pendientes</div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="stat-card text-center p-3">
            <div class="stat-number text-gold">
              S/ <?= number_format(array_sum(array_column($ordenes ?? [], 'total')), 2) ?>
            </div>
            <div class="stat-label">Total gastado</div>
          </div>
        </div>
      </div>

      <!-- Últimas 3 órdenes -->
      <?php if (!empty($ordenes)): ?>
      <div class="admin-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 class="h6 mb-0">Últimas órdenes</h2>
          <a href="<?= url('mi-cuenta/ordenes') ?>" class="btn btn-outline-gold btn-sm">Ver todas</a>
        </div>
        <table class="table table-sm admin-table">
          <thead>
            <tr>
              <th>#</th><th>Fecha</th><th>Estado</th><th>Total</th><th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (array_slice($ordenes, 0, 3) as $o): ?>
            <tr>
              <td><?= (int)$o['id'] ?></td>
              <td><?= date('d/m/Y', strtotime($o['fecha_creacion'])) ?></td>
              <td><span class="status-badge status-<?= e($o['estado']) ?>"><?= e(\App\Models\OrderModel::ESTADOS[$o['estado']] ?? $o['estado']) ?></span></td>
              <td>S/ <?= number_format((float)$o['total'], 2) ?></td>
              <td><a href="<?= url('mi-cuenta/ordenes/' . $o['id']) ?>" class="btn btn-sm btn-outline-secondary">Ver</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

  </div>
</div>
