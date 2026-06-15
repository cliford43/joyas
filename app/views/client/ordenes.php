<?php /* Vista: Historial de órdenes del cliente */ ?>
<div class="container py-5">
  <div class="row g-4">
    <div class="col-lg-3"><?php include APP_PATH . '/views/client/partials/sidebar.php'; ?></div>
    <div class="col-lg-9">
      <h1 class="font-heading mb-4">Mis órdenes</h1>

      <?php if (empty($ordenes)): ?>
        <div class="text-center py-5">
          <i class="bi bi-receipt" style="font-size:3rem;color:rgba(212,175,55,0.3);"></i>
          <p class="mt-3 text-muted">Aún no tienes órdenes.</p>
          <a href="<?= url('catalogo') ?>" class="btn btn-gold mt-2">Explorar joyas</a>
        </div>
      <?php else: ?>
        <div class="admin-card">
          <div class="table-responsive">
            <table class="table admin-table mb-0">
              <thead>
                <tr>
                  <th>#Orden</th>
                  <th>Fecha</th>
                  <th>Estado</th>
                  <th>Método</th>
                  <th class="text-end">Total</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($ordenes as $o): ?>
                <tr>
                  <td><strong>#<?= (int)$o['id'] ?></strong></td>
                  <td><?= date('d/m/Y H:i', strtotime($o['fecha_creacion'])) ?></td>
                  <td>
                    <span class="status-badge status-<?= e($o['estado']) ?>">
                      <?= e(\App\Models\OrderModel::ESTADOS[$o['estado']] ?? $o['estado']) ?>
                    </span>
                  </td>
                  <td>
                    <?= $o['metodo_pago'] === 'transferencia' ? 'Transferencia' : 'Contra entrega' ?>
                  </td>
                  <td class="text-end">S/ <?= number_format((float)$o['total'], 2) ?></td>
                  <td>
                    <a href="<?= url('mi-cuenta/ordenes/' . $o['id']) ?>"
                       class="btn btn-sm btn-outline-gold">Ver detalle</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
