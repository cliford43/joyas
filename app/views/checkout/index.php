<?php
/* Vista: Checkout — datos de entrega y método de pago */
$items   = $summary['items']          ?? [];
$total   = $summary['total']          ?? 0;
$subtotal= $summary['subtotal']       ?? 0;
$descCup = $summary['couponDiscount'] ?? 0;

$banco       = $config['banco_nombre']       ?? '';
$cuenta      = $config['banco_cuenta']       ?? '';
$tipoCuenta  = $config['banco_tipo']         ?? '';
$beneficiario= $config['banco_beneficiario'] ?? '';
?>

<div class="breadcrumb-viluna">
  <div class="container">
    <nav aria-label="Ruta de navegación">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url() ?>">Inicio</a></li>
        <li class="breadcrumb-item"><a href="<?= url('carrito') ?>">Carrito</a></li>
        <li class="breadcrumb-item active">Finalizar compra</li>
      </ol>
    </nav>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <h1 class="font-heading mb-4">Finalizar compra</h1>

    <?php if (!empty($errors)): ?>
      <div class="alert-viluna-error mb-4">
        <ul class="mb-0 ps-3">
          <?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('checkout/confirmar') ?>" enctype="multipart/form-data" id="checkoutForm">
      <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

      <div class="row g-4">
        <!-- ─── Dirección ───────────────────────────────── -->
        <div class="col-lg-7">
          <div class="admin-card">
            <div class="admin-card-header-viluna mb-3">
              <h2 class="h6 mb-0">Dirección de entrega</h2>
            </div>

            <div class="mb-3">
              <label class="form-label-viluna" for="direccion">Dirección completa</label>
              <textarea id="direccion" name="direccion" rows="3"
                        class="form-control form-control-viluna" required
                        placeholder="Calle, número, colonia, ciudad, departamento"><?= e($old['direccion'] ?? ($user['direccion'] ?? '')) ?></textarea>
            </div>
          </div>

          <!-- ─── Método de pago ────────────────────────── -->
          <div class="admin-card mt-3">
            <h2 class="h6 mb-3">Método de pago</h2>

            <div class="form-check mb-3">
              <input class="form-check-input" type="radio" name="metodo_pago"
                     id="contraEntrega" value="contra_entrega"
                     <?= ($old['metodo_pago'] ?? '') !== 'transferencia' ? 'checked' : '' ?>>
              <label class="form-check-label" for="contraEntrega">
                <i class="bi bi-cash-stack me-2 text-gold"></i>
                <strong>Contra entrega</strong>
                <span class="d-block small text-muted ms-4">Paga cuando recibas tu pedido.</span>
              </label>
            </div>

            <div class="form-check mb-3">
              <input class="form-check-input" type="radio" name="metodo_pago"
                     id="transferencia" value="transferencia"
                     <?= ($old['metodo_pago'] ?? '') === 'transferencia' ? 'checked' : '' ?>>
              <label class="form-check-label" for="transferencia">
                <i class="bi bi-bank me-2 text-gold"></i>
                <strong>Transferencia bancaria</strong>
                <span class="d-block small text-muted ms-4">Transfiere y sube tu comprobante.</span>
              </label>
            </div>

            <!-- Datos bancarios (visible solo con transferencia) -->
            <div id="bancoDatos" style="display:none;" class="p-3 mt-2" style="background:var(--color-light-gray);border:var(--border-gold);">
              <p class="small mb-2"><strong>Datos de transferencia:</strong></p>
              <ul class="list-unstyled small mb-3">
                <li><strong>Banco:</strong> <?= e($banco) ?></li>
                <li><strong>Cuenta:</strong> <?= e($cuenta) ?></li>
                <li><strong>Tipo:</strong> <?= e($tipoCuenta) ?></li>
                <li><strong>Beneficiario:</strong> <?= e($beneficiario) ?></li>
              </ul>
              <label class="form-label-viluna" for="comprobante">Subir comprobante (JPG, PNG o PDF, máx. 5 MB)</label>
              <input type="file" id="comprobante" name="comprobante"
                     class="form-control form-control-viluna"
                     accept=".jpg,.jpeg,.png,.pdf"
                     aria-describedby="comprobanteHelp">
              <div id="comprobanteHelp" class="form-text">El comprobante será revisado por nuestro equipo.</div>
            </div>
          </div>
        </div>

        <!-- ─── Resumen ───────────────────────────────────── -->
        <div class="col-lg-5">
          <div class="cart-summary">
            <h2 class="h6 text-uppercase letter-spacing mb-3" style="font-size:0.75rem;">
              Tu pedido
            </h2>
            <?php foreach ($items as $item):
              $precioItem = max(0, (float)$item['precio'] - (float)$item['descuento']);
            ?>
            <div class="d-flex justify-content-between small mb-2">
              <span><?= e($item['nombre']) ?> × <?= (int)$item['cantidad'] ?></span>
              <span><?= formatPrice($precioItem * (int)$item['cantidad']) ?></span>
            </div>
            <?php endforeach; ?>
            <hr style="border-color:rgba(212,175,55,0.2);">
            <div class="summary-row"><span>Subtotal</span><span><?= formatPrice($subtotal) ?></span></div>
            <?php if ($descCup > 0): ?>
            <div class="summary-row text-success"><span>Descuento</span><span>-<?= formatPrice($descCup) ?></span></div>
            <?php endif; ?>
            <div class="summary-row total-row"><span>Total</span><span><?= formatPrice($total) ?></span></div>

            <button type="submit" class="btn btn-gold w-100 mt-4" id="submitBtn" data-loading-text="Confirmando...">
              <i class="bi bi-lock me-1"></i>Confirmar pedido
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>

<script>
  // Mostrar/ocultar datos bancarios según método de pago
  document.querySelectorAll('[name="metodo_pago"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
      const bancoDatos = document.getElementById('bancoDatos');
      bancoDatos.style.display = this.value === 'transferencia' ? 'block' : 'none';
      document.getElementById('comprobante').required = this.value === 'transferencia';
    });
  });
  // Estado inicial
  const selected = document.querySelector('[name="metodo_pago"]:checked');
  if (selected && selected.value === 'transferencia') {
    document.getElementById('bancoDatos').style.display = 'block';
    document.getElementById('comprobante').required = true;
  }
</script>
