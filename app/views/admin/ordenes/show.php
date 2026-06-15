<?php /* Admin: Detalle de orden */ ?>
<div class="admin-page-header">
  <h1>Orden #<?= (int)$orden['id'] ?></h1>
  <a href="<?= url('admin/ordenes') ?>" class="btn btn-outline-secondary btn-sm">Volver</a>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="admin-card mb-3">
      <h2 class="h6 mb-3">Productos</h2>
      <table class="table table-sm admin-table mb-0">
        <thead><tr><th>Producto</th><th class="text-center">Cant.</th><th class="text-end">Precio</th><th class="text-end">Subtotal</th></tr></thead>
        <tbody>
          <?php foreach ($items as $item):
            $precioUnit = (float)$item['precio_unitario'] - (float)$item['descuento_unitario'];
          ?>
          <tr>
            <td><?= e($item['nombre']) ?></td>
            <td class="text-center"><?= (int)$item['cantidad'] ?></td>
            <td class="text-end"><?= formatPrice($precioUnit) ?></td>
            <td class="text-end"><?= formatPrice($precioUnit * (int)$item['cantidad']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <?php if ((float)$orden['descuento_cupon'] > 0): ?>
          <tr><td colspan="3" class="text-end text-success">Descuento cupón:</td>
              <td class="text-end text-success">-<?= formatPrice((float)$orden['descuento_cupon']) ?></td></tr>
          <?php endif; ?>
          <tr><td colspan="3" class="text-end fw-bold">Total:</td>
              <td class="text-end fw-bold text-gold"><?= formatPrice((float)$orden['total']) ?></td></tr>
        </tfoot>
      </table>
    </div>
    <!-- Comprobante -->
    <?php if (!empty($orden['comprobante_ruta'])): ?>
    <div class="admin-card">
      <h2 class="h6 mb-2">Comprobante de transferencia</h2>
      <?php if (str_ends_with(strtolower($orden['comprobante_ruta']), '.pdf')): ?>
        <a href="/<?= e(ltrim($orden['comprobante_ruta'], '/')) ?>" target="_blank" class="btn btn-outline-gold btn-sm">
          <i class="bi bi-file-pdf me-1"></i>Ver PDF
        </a>
      <?php else: ?>
        <img src="/<?= e(ltrim($orden['comprobante_ruta'], '/')) ?>" alt="Comprobante"
             style="max-width:100%;max-height:300px;border:var(--border-gold);">
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
  <div class="col-lg-4">
    <div class="admin-card">
      <h2 class="h6 mb-3">Info de la orden</h2>
      <p><strong>Cliente:</strong> <?= e($orden['nombre'] . ' ' . $orden['apellido']) ?></p>
      <p><strong>Correo:</strong> <?= e($orden['correo']) ?></p>
      <p><strong>Método:</strong> <?= $orden['metodo_pago'] === 'transferencia' ? 'Transferencia' : 'Contra entrega' ?></p>
      <p><strong>Dirección:</strong> <?= e($orden['direccion_entrega']) ?></p>
      <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($orden['fecha_creacion'])) ?></p>
      <hr>
      <h2 class="h6 mb-2">Cambiar estado</h2>
      <form method="POST" action="<?= url('admin/ordenes/' . $orden['id'] . '/estado') ?>">
        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
        <select name="estado" class="form-select form-select-sm mb-2">
          <?php foreach ($estados as $k => $v): ?>
            <option value="<?= $k ?>" <?= $orden['estado'] === $k ? 'selected' : '' ?>><?= e($v) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-gold btn-sm w-100">Actualizar estado</button>
      </form>
    </div>
  </div>
</div>
