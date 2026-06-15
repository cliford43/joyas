/**
 * VILUNA — admin.js
 * Scripts del panel administrativo.
 */

'use strict';

// ─── Preview de imágenes antes de subir ───────────────────────
document.addEventListener('change', function (e) {
  const input = e.target;
  if (!input.matches('input[type="file"][data-preview]')) return;

  const previewId = input.dataset.preview;
  const container = document.getElementById(previewId);
  if (!container) return;

  container.innerHTML = '';
  const files = Array.from(input.files).slice(0, 10);

  files.forEach(file => {
    if (!file.type.startsWith('image/')) return;
    const reader = new FileReader();
    reader.onload = function (ev) {
      const div = document.createElement('div');
      div.className = 'img-item';
      div.innerHTML = `<img src="${ev.target.result}" alt="preview">`;
      container.appendChild(div);
    };
    reader.readAsDataURL(file);
  });
});

// ─── Confirmar acciones destructivas ─────────────────────────
document.addEventListener('submit', function (e) {
  const form = e.target;
  if (!form.dataset.confirm) return;
  if (!confirm(form.dataset.confirm)) e.preventDefault();
});

document.addEventListener('click', function (e) {
  const btn = e.target.closest('[data-confirm]');
  if (!btn) return;
  if (!confirm(btn.dataset.confirm)) e.preventDefault();
});

// ─── Auto-cerrar alerts después de 4 s ───────────────────────
document.querySelectorAll('.alert').forEach(el => {
  setTimeout(() => {
    const bsAlert = window.bootstrap?.Alert?.getOrCreateInstance(el);
    if (bsAlert) bsAlert.close();
  }, 4000);
});
