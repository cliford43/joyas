/**
 * VILUNA — loading.js
 * Módulo de estados de carga para botones de acción.
 * Previene envíos duplicados y muestra feedback visual.
 */

'use strict';

(function () {
  var DEFAULT_TEXT = 'Procesando...';
  var TIMEOUT_MS  = 10000;

  /** @type {WeakMap<HTMLButtonElement, {originalHTML: string, originalDisabled: boolean, timeoutId: number|null}>} */
  var btnState = new WeakMap();

  /**
   * Aplica estado de carga a un botón.
   * @param {HTMLButtonElement} btn - El botón objetivo
   * @param {string} [loadingText] - Texto a mostrar durante carga
   * @returns {Function} restore - Función para restaurar el botón
   */
  function startLoading(btn, loadingText) {
    if (!btn || btnState.has(btn)) return function () {};

    var text = loadingText || btn.getAttribute('data-loading-text') || DEFAULT_TEXT;

    btnState.set(btn, {
      originalHTML: btn.innerHTML,
      originalDisabled: btn.disabled,
      timeoutId: null
    });

    btn.disabled = true;
    btn.classList.add('btn-loading');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + text;

    var timeoutId = setTimeout(function () {
      stopLoading(btn);
    }, TIMEOUT_MS);

    btnState.get(btn).timeoutId = timeoutId;

    return function () { stopLoading(btn); };
  }

  /**
   * Restaura un botón a su estado original.
   * @param {HTMLButtonElement} btn - El botón a restaurar
   */
  function stopLoading(btn) {
    if (!btn || !btnState.has(btn)) return;

    var state = btnState.get(btn);

    if (state.timeoutId !== null) {
      clearTimeout(state.timeoutId);
    }

    btn.innerHTML = state.originalHTML;
    btn.disabled = state.originalDisabled;
    btn.classList.remove('btn-loading');

    btnState.delete(btn);
  }

  // ─── Automatic form submit interception ────────────────────────
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form || form.tagName !== 'FORM') return;

    var btn = form.querySelector('button[data-loading-text], button.btn-loading, input[type="submit"][data-loading-text]');
    if (!btn) return;

    startLoading(btn);
  });

  // Exponer globalmente
  window.startLoading = startLoading;
  window.stopLoading  = stopLoading;
})();
