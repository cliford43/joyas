<?php
/* Vista: Carrito de compras */
$items   = $summary['items']          ?? [];
$coupon  = $summary['coupon']         ?? null;
$subtotal= $summary['subtotal']       ?? 0;
$descCup = $summary['couponDiscount'] ?? 0;
$total   = $summary['total']          ?? 0;
?>

<div class="breadcrumb-viluna">
  <div class="container">
    <nav aria-label="Ruta de navegación">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url() ?>">Inicio</a></li>
        <li class="breadcrumb-item active">Mi carrito</li>
      </ol>
    </nav>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <h1 class="font-heading mb-4">Mi carrito
      <span class="small text-gold" style="font-size:1rem;" id="cartCountLabel">
        (<?= array_sum(array_column($items, 'cantidad')) ?> ítems)
      </span>
    </h1>

    <?php if (empty($items)): ?>
      <div class="text-center py-5">
        <i class="bi bi-bag-x" style="font-size:4rem;color:rgba(212,175,55,0.3);"></i>
        <p class="mt-3" style="color:var(--color-gray);">Tu carrito está vacío.</p>
        <a href="<?= url('catalogo') ?>" class="btn btn-gold mt-2">Explorar catálogo</a>
      </div>
    <?php else: ?>

      <div class="row g-4">
        <!-- ─── Tabla de ítems ───────────────────────────── -->
        <div class="col-lg-8">
          <table class="cart-table w-100 table" aria-label="Productos en el carrito">
            <thead>
              <tr>
                <th scope="col">Producto</th>
                <th scope="col" class="text-center">Cantidad</th>
                <th scope="col" class="text-end">Subtotal</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody id="cartBody">
              <?php foreach ($items as $pid => $item):
                $precioItem  = max(0, (float)$item['precio'] - (float)$item['descuento']);
                $subtotalItem= $precioItem * (int)$item['cantidad'];
                $imgUrl      = $item['imagen_principal'] ? '/' . ltrim($item['imagen_principal'], '/') : asset('images/placeholder-joya.jpg');
              ?>
              <tr id="row-<?= (int)$pid ?>">
                <!-- Producto -->
                <td>
                  <div class="d-flex align-items-center gap-3">
                    <img src="<?= e($imgUrl) ?>" alt="<?= e($item['nombre']) ?>"
                         style="width:70px;height:70px;object-fit:cover;border:var(--border-gold);">
                    <div>
                      <a href="<?= url('producto/' . ($item['slug'] ?? '')) ?>"
                         class="text-black text-decoration-none fw-bold" style="font-size:0.95rem;">
                        <?= e($item['nombre']) ?>
                      </a>
                      <div style="font-size:0.8rem;color:var(--color-gray);">
                        S/ <?= number_format($precioItem, 2) ?> c/u
                      </div>
                    </div>
                  </div>
                </td>
                <!-- Cantidad -->
                <td class="text-center" style="width:120px;">
                  <input type="number" class="qty-input cart-qty"
                         value="<?= (int)$item['cantidad'] ?>"
                         min="1" max="<?= (int)$item['stock'] ?>"
                         data-product-id="<?= (int)$pid ?>"
                         aria-label="Cantidad de <?= e($item['nombre']) ?>">
                </td>
                <!-- Subtotal -->
                <td class="text-end fw-bold" id="subtotal-<?= (int)$pid ?>">
                  S/ <?= number_format($subtotalItem, 2) ?>
                </td>
                <!-- Eliminar -->
                <td class="text-end" style="width:40px;">
                  <button class="btn btn-link text-danger p-0 cart-remove"
                          data-product-id="<?= (int)$pid ?>"
                          aria-label="Eliminar <?= e($item['nombre']) ?>">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <!-- Acciones -->
          <div class="d-flex justify-content-between align-items-center mt-2">
            <a href="<?= url('catalogo') ?>" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-arrow-left me-1"></i>Seguir comprando
            </a>
            <form method="POST" action="<?= url('carrito/vaciar') ?>"
                  data-confirm="¿Vaciar todo el carrito?">
              <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
              <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash me-1"></i>Vaciar carrito
              </button>
            </form>
          </div>
        </div>

        <!-- ─── Resumen ───────────────────────────────────── -->
        <div class="col-lg-4">
          <div class="cart-summary">
            <h2 class="h6 text-uppercase letter-spacing mb-3" style="font-size:0.75rem;">
              Resumen del pedido
            </h2>

            <div class="summary-row">
              <span>Subtotal</span>
              <span id="summarySubtotal">S/ <?= number_format($subtotal, 2) ?></span>
            </div>

            <?php if ($coupon): ?>
            <div class="summary-row text-success">
              <span>Descuento (<?= e($coupon['codigo']) ?> <?= $coupon['porcentaje'] ?>%)</span>
              <span id="summaryCouponDiscount">-S/ <?= number_format($descCup, 2) ?></span>
            </div>
            <?php else: ?>
            <div class="summary-row" id="couponDiscountRow" style="display:none;">
              <span id="couponLabel">Descuento cupón</span>
              <span id="summaryCouponDiscount"></span>
            </div>
            <?php endif; ?>

            <div class="summary-row total-row">
              <span>Total</span>
              <span id="summaryTotal">S/ <?= number_format($total, 2) ?></span>
            </div>

            <!-- Cupón -->
            <?php if (!$coupon): ?>
            <div class="mt-3">
              <label class="form-label-viluna" for="cuponInput">Cupón de descuento</label>
              <div class="d-flex gap-1">
                <input type="text" id="cuponInput" placeholder="Código"
                       class="form-control form-control-viluna"
                       style="text-transform:uppercase;"
                       aria-label="Código de cupón">
                <button id="applyCouponBtn" class="btn btn-outline-gold btn-sm px-3">
                  Aplicar
                </button>
              </div>
              <div id="couponMsg" class="mt-1 small" aria-live="polite"></div>
            </div>
            <?php else: ?>
            <form method="POST" action="<?= url('cupon/quitar') ?>" class="mt-2">
              <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                <i class="bi bi-x me-1"></i>Quitar cupón <?= e($coupon['codigo']) ?>
              </button>
            </form>
            <?php endif; ?>

            <a href="<?= url('checkout') ?>" class="btn btn-gold w-100 mt-4">
              Proceder al pago <i class="bi bi-arrow-right ms-1"></i>
            </a>
          </div>
        </div>
      </div>

    <?php endif; ?>
  </div>
</section>

<?php $extraJs = '<script src="' . asset('js/cart.js') . '"></script>'; ?>
