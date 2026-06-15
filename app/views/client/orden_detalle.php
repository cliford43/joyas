<?php
/* Vista: Detalle de una orden del cliente */
$estadoLabel = \App\Models\OrderModel::ESTADOS[$orden['estado'] ?? 'pendiente'] ?? '';
$metodoPago  = $orden['metodo_pago'] === 'transferencia' ? 'Transferencia bancaria' : 'Contra entrega';
?>
<div class="container py-5">
  <div class="row g-4">
    <div class="col-lg-3"><?php include APP_PATH . '/views/client/partials/sidebar.php'; ?></div>
    <div class="col-lg-9">
      <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= url('mi-cuenta/ordenes') ?>" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="font-heading mb-0">Orden #<?= (int)$orden['id'] ?></h1>
        <span class="status-badge status-<?= e($orden['estado']) ?>"><?= e($estadoLabel) ?></span>
      </div>

      <!-- Productos -->
      <div class="admin-card mb-3">
        <h2 class="h6 mb-3">Productos</h2>
        <table class="table table-sm admin-table mb-0">
          <thead>
            <tr><th>Producto</th><th class="text-center">Cant.</th><th class="text-end">Precio unit.</th><th class="text-end">Subtotal</th></tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item):
              $precioUnit = (float)$item['precio_unitario'] - (float)$item['descuento_unitario'];
            ?>
            <tr>
              <td>
                <a href="<?= url('producto/' . ($item['slug'] ?? '')) ?>" class="text-black">
                  <?= e($item['nombre']) ?>
                </a>
              </td>
              <td class="text-center"><?= (int)$item['cantidad'] ?></td>
              <td class="text-end">S/ <?= number_format($precioUnit, 2) ?></td>
              <td class="text-end">S/ <?= number_format($precioUnit * (int)$item['cantidad'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
              <td class="text-end">S/ <?= number_format((float)$orden['subtotal'], 2) ?></td>
            </tr>
            <?php if ((float)$orden['descuento_cupon'] > 0): ?>
            <tr>
              <td colspan="3" class="text-end text-success">Descuento cupón:</td>
              <td class="text-end text-success">-S/ <?= number_format((float)$orden['descuento_cupon'], 2) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
              <td colspan="3" class="text-end"><strong>Total:</strong></td>
              <td class="text-end"><strong class="text-gold">S/ <?= number_format((float)$orden['total'], 2) ?></strong></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Info de entrega -->
      <div class="admin-card">
        <h2 class="h6 mb-3">Información de entrega</h2>
        <div class="row g-3 small">
          <div class="col-sm-6">
            <div class="text-muted">Método de pago</div>
            <strong><?= e($metodoPago) ?></strong>
          </div>
          <div class="col-sm-6">
            <div class="text-muted">Fecha de orden</div>
            <strong><?= date('d/m/Y H:i', strtotime($orden['fecha_creacion'])) ?></strong>
          </div>
          <div class="col-12">
            <div class="text-muted">Dirección de entrega</div>
            <strong><?= e($orden['direccion_entrega'] ?? '') ?></strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
