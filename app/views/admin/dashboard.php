<?php /* Vista: Dashboard Admin */ ?>

<div class="admin-page-header">
  <h1>Dashboard</h1>
  <span class="small text-muted"><?= date('d/m/Y') ?></span>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="admin-stat">
      <div class="stat-icon"><i class="bi">Q</i></div>
      <div class="stat-number">Q <?= number_format($totalVentas ?? 0, 0) ?></div>
      <div class="stat-label">Ventas totales</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="admin-stat">
      <div class="stat-icon"><i class="bi bi-people"></i></div>
      <div class="stat-number"><?= $totalUsuarios ?? 0 ?></div>
      <div class="stat-label">Usuarios</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="admin-stat">
      <div class="stat-icon"><i class="bi bi-gem"></i></div>
      <div class="stat-number"><?= $totalProductos ?? 0 ?></div>
      <div class="stat-label">Productos activos</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="admin-stat">
      <div class="stat-icon"><i class="bi bi-receipt"></i></div>
      <div class="stat-number"><?= count($ordenesRecientes ?? []) ?></div>
      <div class="stat-label">Órdenes recientes</div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Gráfica de ventas -->
  <div class="col-lg-8">
    <div class="admin-card">
      <div class="admin-card-header-viluna">
        <h2>Ventas últimos 12 meses</h2>
      </div>
      <canvas id="salesChart" height="100" aria-label="Gráfica de ventas mensuales" role="img"></canvas>
    </div>
  </div>

  <!-- Top productos -->
  <div class="col-lg-4">
    <div class="admin-card">
      <div class="admin-card-header-viluna">
        <h2>Más vendidos</h2>
      </div>
      <?php if (!empty($masVendidos)): ?>
        <ol class="list-group list-group-numbered list-group-flush">
          <?php foreach ($masVendidos as $p): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2"
                style="font-size:0.85rem;">
              <span><?= e($p['nombre']) ?></span>
              <span class="badge" style="background:rgba(212,175,55,0.15);color:#D4AF37;">
                <?= (int)($p['total_vendido'] ?? 0) ?> uds.
              </span>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php else: ?>
        <p class="text-muted small">Sin datos de ventas aún.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Órdenes recientes -->
  <div class="col-12">
    <div class="admin-card">
      <div class="admin-card-header-viluna">
        <h2>Órdenes recientes</h2>
        <a href="<?= url('admin/ordenes') ?>" class="btn btn-outline-gold btn-sm">Ver todas</a>
      </div>
      <div class="table-responsive">
        <table class="table admin-table mb-0">
          <thead>
            <tr><th>#</th><th>Cliente</th><th>Estado</th><th>Método</th><th class="text-end">Total</th><th></th></tr>
          </thead>
          <tbody>
            <?php foreach ($ordenesRecientes ?? [] as $o): ?>
            <tr>
              <td>#<?= (int)$o['id'] ?></td>
              <td><?= e($o['nombre'] . ' ' . $o['apellido']) ?></td>
              <td><span class="status-badge status-<?= e($o['estado']) ?>"><?= e(\App\Models\OrderModel::ESTADOS[$o['estado']] ?? '') ?></span></td>
              <td><?= $o['metodo_pago'] === 'transferencia' ? 'Transferencia' : 'Contra entrega' ?></td>
              <td class="text-end"><?= formatPrice((float)$o['total']) ?></td>
              <td><a href="<?= url('admin/ordenes/' . $o['id']) ?>" class="btn btn-sm btn-outline-secondary">Ver</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php
$mesesJson  = json_encode(array_map(fn($m) => date('M Y', strtotime($m . '-01')), $meses ?? []));
$ventasJson = json_encode(array_map('floatval', $ventas ?? []));
$extraJs = <<<JS
<script>
(function() {
  const ctx = document.getElementById('salesChart');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: {$mesesJson},
      datasets: [{
        label: 'Ventas (Q)',
        data: {$ventasJson},
        backgroundColor: 'rgba(212,175,55,0.25)',
        borderColor: '#D4AF37',
        borderWidth: 2,
        borderRadius: 4,
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { callback: v => 'Q ' + v.toLocaleString() } },
        x: { grid: { display: false } }
      }
    }
  });
})();
</script>
JS;
?>
