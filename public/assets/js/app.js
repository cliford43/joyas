/**
 * VILUNA — app.js
 * Scripts globales del frontend público.
 */

'use strict';

// ─── Navbar scroll effect ─────────────────────────────────────
(function () {
  const nav = document.getElementById('mainNav');
  if (!nav) return;
  const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 50);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
})();

// ─── Newsletter AJAX ──────────────────────────────────────────
(function () {
  const form = document.getElementById('newsletterForm');
  const msg  = document.getElementById('newsletterMsg');
  if (!form || !msg) return;

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    const data = new FormData(form);
    try {
      const res  = await fetch(form.action, {
        method: 'POST',
        body: data,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      });
      const json = await res.json();
      msg.textContent = json.message || '';
      msg.style.color = json.success ? 'var(--color-gold)' : '#DC3545';
      if (json.success) form.reset();
    } catch {
      msg.textContent = 'Error al suscribirse. Intenta de nuevo.';
      msg.style.color = '#DC3545';
    }
  });
})();

// ─── Wishlist toggle AJAX ─────────────────────────────────────
document.addEventListener('click', async function (e) {
  const btn = e.target.closest('[data-wishlist]');
  if (!btn) return;
  e.preventDefault();

  const productId = btn.dataset.wishlist;
  const csrf      = document.querySelector('meta[name="csrf-token"]')?.content
                    || document.querySelector('[name="_csrf_token"]')?.value || '';
  try {
    const res  = await fetch('/wishlist/toggle', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: `producto_id=${productId}&_csrf_token=${encodeURIComponent(csrf)}`,
    });
    const json = await res.json();
    if (json.success) {
      btn.classList.toggle('active', json.inWishlist);
      const icon = btn.querySelector('i');
      if (icon) icon.className = json.inWishlist ? 'bi bi-heart-fill' : 'bi bi-heart';
    }
  } catch {}
});

// ─── Imagen principal producto (galería) ──────────────────────
document.addEventListener('click', function (e) {
  const thumb = e.target.closest('.thumb');
  if (!thumb) return;
  const main = document.querySelector('.main-image img');
  if (!main) return;
  main.src = thumb.src;
  document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
  thumb.classList.add('active');
});

// ─── Star Rating Selector (Reseñas) ──────────────────────────
(function () {
  const container = document.getElementById('starRatingInput');
  const input     = document.getElementById('calificacionInput');
  if (!container || !input) return;

  const stars = container.querySelectorAll('.star-selectable');
  let currentValue = parseInt(input.value, 10) || 0;

  // Initialize from existing value (e.g. validation error reload)
  if (currentValue > 0) {
    fillStars(currentValue);
  }

  function fillStars(n) {
    stars.forEach((star, idx) => {
      if (idx < n) {
        star.classList.remove('bi-star');
        star.classList.add('bi-star-fill');
        star.setAttribute('aria-checked', 'true');
      } else {
        star.classList.remove('bi-star-fill');
        star.classList.add('bi-star');
        star.setAttribute('aria-checked', 'false');
      }
    });
  }

  // Hover: highlight stars up to hovered
  container.addEventListener('mouseover', function (e) {
    const star = e.target.closest('.star-selectable');
    if (!star) return;
    const val = parseInt(star.dataset.value, 10);
    fillStars(val);
  });

  // Mouse leave: revert to selected value
  container.addEventListener('mouseleave', function () {
    fillStars(currentValue);
  });

  // Click: set rating value
  container.addEventListener('click', function (e) {
    const star = e.target.closest('.star-selectable');
    if (!star) return;
    currentValue = parseInt(star.dataset.value, 10);
    input.value = currentValue;
    fillStars(currentValue);
  });

  // Keyboard support for accessibility
  container.addEventListener('keydown', function (e) {
    const star = e.target.closest('.star-selectable');
    if (!star) return;
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      currentValue = parseInt(star.dataset.value, 10);
      input.value = currentValue;
      fillStars(currentValue);
    }
  });
})();
