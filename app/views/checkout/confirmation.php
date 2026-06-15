<?php
/* Vista: Confirmación de orden */
$metodoPagoLabel = [
    'contra_entrega' => 'Contra entrega',
    'transferencia'  => 'Transferencia bancaria',
];
$estadoLabel = \App\Models\OrderModel::ESTADOS[$order['estado'] ?? 'pendiente'] ?? 'Pendiente';
?>

<section class="py-5" style="min-height:70vh;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-7 text-center">
        <div style="width:80px;height:80px;background:rgba(212,175,55,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
          <i class="bi bi-check-lg" style="font-size:2.5rem;color:var(--color-gold);"></i>
        </div>

        <h1 class="font-heading mb-2">¡Orden confirmada!</h1>
        <p class="text-gold letter-spacing mb-4" style="font-size:0.85rem;">
          ORDEN #<?= (int)$order['id'] ?>
        </p>
        <p style="color:var(--color-gray);margin-bottom:2rem;">
          Gracias por tu compra. Recibirás un correo con los detalles de tu pedido.
        </p>

        <!-- Resumen -->
        <div class="admin-card text-start mb-4">
          <table class="table table-sm mb-0">
            <thead>
              <tr>
                <th style="font-size:0.75rem;letter-spacing:2px;text-transform:uppercase;color:var(--color-gray);">Producto</th>
                <th class="text-center" style="font-size:0.75rem;">Cant.</th>
                <th class="text-end" style="font-size:0.75rem;">Precio</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item):
                $precioUnitario = (float)$item['precio_unitario'] - (float)$item['descuento_unitario'];
              ?>
              <tr>
                <td><?= e($item['nombre']) ?></td>
                <td class="text-center"><?= (int)$item['cantidad'] ?></td>
                <td class="text-end"><?= formatPrice($precioUnitario * (int)$item['cantidad']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <hr style="border-color:rgba(212,175,55,0.2);">
          <div class="d-flex justify-content-between px-1">
            <strong>Total pagado</strong>
            <strong class="text-gold"><?= formatPrice((float)$order['total']) ?></strong>
          </div>
        </div>

        <!-- Detalles -->
        <div class="admin-card text-start mb-4">
          <div class="row g-3 small">
            <div class="col-6">
              <div class="text-muted mb-1">Método de pago</div>
              <strong><?= e($metodoPagoLabel[$order['metodo_pago'] ?? ''] ?? $order['metodo_pago'] ?? '') ?></strong>
            </div>
            <div class="col-6">
              <div class="text-muted mb-1">Estado</div>
              <span class="status-badge status-<?= e($order['estado'] ?? 'pendiente') ?>">
                <?= e($estadoLabel) ?>
              </span>
            </div>
            <div class="col-12">
              <div class="text-muted mb-1">Dirección de entrega</div>
              <strong><?= e($order['direccion_entrega'] ?? '') ?></strong>
            </div>
          </div>
        </div>

        <?php if (($order['metodo_pago'] ?? '') === 'transferencia' && !empty($order['comprobante_ruta'])): ?>
        <div class="alert-viluna-success mb-4">
          <i class="bi bi-file-check me-2"></i>
          Tu comprobante fue recibido y está pendiente de revisión.
        </div>
        <?php endif; ?>

        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a href="<?= url('mi-cuenta/ordenes') ?>" class="btn btn-gold">
            <i class="bi bi-receipt me-1"></i>Ver mis órdenes
          </a>
          <a href="<?= url('catalogo') ?>" class="btn btn-outline-gold">
            Seguir comprando
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
