/**
 * VILUNA — cart.js
 * Gestión AJAX del carrito: actualizar cantidad, eliminar, aplicar cupón.
 */

'use strict';

(function () {

  const csrfToken = document.querySelector('[name="_csrf_token"]')?.value || '';

  // ─── Helper: petición POST AJAX ───────────────────────────
  async function postAjax(url, body) {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: new URLSearchParams({ ...body, _csrf_token: csrfToken }).toString(),
    });
    return res.json();
  }

  // ─── Actualizar totales en UI ──────────────────────────────
  function updateSummary(data) {
    const fmt = v => 'Q ' + parseFloat(v).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    const subtotalEl = document.getElementById('summarySubtotal');
    const totalEl    = document.getElementById('summaryTotal');
    const countEl    = document.getElementById('cartCountLabel');
    const discountRow= document.getElementById('couponDiscountRow');
    const discountEl = document.getElementById('summaryCouponDiscount');

    if (subtotalEl) subtotalEl.textContent = fmt(data.subtotal ?? data.total);
    if (totalEl)    totalEl.textContent    = fmt(data.total);
    if (countEl)    countEl.textContent    = '(' + (data.totalItems ?? 0) + ' ítems)';

    // Actualizar badge del navbar
    document.querySelectorAll('.cart-badge').forEach(b => {
      b.textContent = data.totalItems ?? 0;
      b.style.display = (data.totalItems ?? 0) > 0 ? '' : 'none';
    });

    if (discountRow && data.couponDiscount > 0) {
      discountRow.style.display = '';
      if (discountEl) discountEl.textContent = '-' + fmt(data.couponDiscount);
    }
  }

  // ─── Cambio de cantidad ────────────────────────────────────
  let qtyTimer = null;
  document.addEventListener('change', async function (e) {
    const input = e.target.closest('.cart-qty');
    if (!input) return;

    clearTimeout(qtyTimer);
    qtyTimer = setTimeout(async () => {
      const productId = input.dataset.productId;
      const qty       = parseInt(input.value);

      try {
        const data = await postAjax('/carrito/actualizar', {
          producto_id: productId,
          cantidad: qty,
        });

        if (data.success) {
          // Actualizar subtotal de la fila
          const subtotalEl = document.getElementById('subtotal-' + productId);
          // Recalcular visualmente (precio unitario × cantidad)
          updateSummary(data);
        } else {
          // Revertir al valor válido más cercano
          const max = parseInt(input.max);
          const min = parseInt(input.min);
          input.value = Math.max(min, Math.min(max, qty));
        }
      } catch {}
    }, 400);
  });

  // ─── Eliminar ítem ─────────────────────────────────────────
  document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.cart-remove');
    if (!btn) return;

    const productId = btn.dataset.productId;
    const row       = document.getElementById('row-' + productId);

    try {
      const data = await postAjax('/carrito/eliminar', { producto_id: productId });

      if (data.success) {
        if (row) {
          row.style.transition = 'opacity 0.3s';
          row.style.opacity    = '0';
          setTimeout(() => row.remove(), 300);
        }
        updateSummary(data);

        // Si carrito queda vacío, recargar para mostrar estado vacío
        if ((data.totalItems ?? 0) === 0) {
          setTimeout(() => location.reload(), 350);
        }
      }
    } catch {}
  });

  // ─── Aplicar cupón AJAX ────────────────────────────────────
  const applyBtn  = document.getElementById('applyCouponBtn');
  const couponMsg = document.getElementById('couponMsg');

  if (applyBtn) {
    applyBtn.addEventListener('click', async function () {
      const input = document.getElementById('cuponInput');
      const code  = input?.value.trim().toUpperCase();
      if (!code) return;

      if (typeof window.startLoading === 'function') {
        window.startLoading(applyBtn, 'Aplicando...');
      } else {
        applyBtn.disabled = true;
      }
      try {
        const data = await postAjax('/cupon/aplicar', { codigo: code });

        if (couponMsg) {
          couponMsg.textContent = data.message || '';
          couponMsg.style.color = data.success ? 'var(--color-gold)' : '#DC3545';
        }

        if (data.success) {
          updateSummary({
            subtotal:       data.subtotal       ?? CartModel?.getSubtotal?.() ?? 0,
            couponDiscount: data.couponDiscount  ?? 0,
            total:          data.total           ?? 0,
            totalItems:     parseInt(document.getElementById('cartCountLabel')?.textContent) || 0,
          });
          // Recargar para mostrar el cupón aplicado en el resumen
          setTimeout(() => location.reload(), 800);
        }
      } catch {
        if (couponMsg) {
          couponMsg.textContent = 'Error al aplicar el cupón.';
          couponMsg.style.color = '#DC3545';
        }
      } finally {
        if (typeof window.stopLoading === 'function') {
          window.stopLoading(applyBtn);
        } else {
          applyBtn.disabled = false;
        }
      }
    });

    // También aplicar con Enter
    document.getElementById('cuponInput')?.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') { e.preventDefault(); applyBtn.click(); }
    });
  }

})();
