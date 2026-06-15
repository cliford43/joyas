/**
 * VILUNA — search.js
 * Buscador avanzado con AJAX y debounce.
 */

'use strict';

(function () {
  const form      = document.getElementById('searchForm');
  const grid      = document.getElementById('productsGrid');
  const countEl   = document.getElementById('searchCount');
  const errorEl   = document.getElementById('searchError');
  const paginEl   = document.getElementById('paginacion');

  if (!form || !grid) return;

  let debounceTimer = null;
  let currentPage   = 1;

  /** Recopila todos los filtros del formulario */
  function getFilters(page = 1) {
    const data = new FormData(form);
    const params = new URLSearchParams();
    for (const [k, v] of data.entries()) {
      if (v !== '') params.set(k, v);
    }
    params.set('pagina', page);
    return params;
  }

  /** Ejecuta la búsqueda AJAX */
  async function doSearch(page = 1) {
    currentPage = page;
    const params = getFilters(page);

    // Limpiar error de precio
    if (errorEl) errorEl.textContent = '';

    // Indicador de carga
    grid.style.opacity = '0.5';

    try {
      const res  = await fetch('/buscar?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      });
      const json = await res.json();

      if (json.precioError) {
        if (errorEl) {
          errorEl.textContent = 'El precio mínimo no puede ser mayor al precio máximo.';
          errorEl.style.color = '#DC3545';
        }
        grid.style.opacity = '1';
        return;
      }

      grid.innerHTML = json.html;
      grid.style.opacity = '1';

      if (countEl) {
        countEl.textContent = json.total > 0
          ? json.total + (json.total === 1 ? ' resultado' : ' resultados')
          : '';
      }

      // Actualizar paginación
      if (paginEl) renderPagination(json.pages, json.currentPage);

      // Actualizar URL sin recargar
      const newUrl = '/buscar?' + params.toString();
      window.history.pushState({}, '', newUrl);

    } catch {
      grid.style.opacity = '1';
    }
  }

  /** Renderiza la paginación */
  function renderPagination(pages, current) {
    if (!paginEl || pages <= 1) {
      if (paginEl) paginEl.innerHTML = '';
      return;
    }

    let html = '<nav aria-label="Paginación"><ul class="pagination justify-content-center">';

    // Anterior
    html += `<li class="page-item ${current === 1 ? 'disabled' : ''}">
      <button class="page-link" data-page="${current - 1}" ${current === 1 ? 'disabled' : ''}>
        &laquo;
      </button>
    </li>`;

    for (let i = 1; i <= pages; i++) {
      html += `<li class="page-item ${i === current ? 'active' : ''}">
        <button class="page-link" data-page="${i}">${i}</button>
      </li>`;
    }

    // Siguiente
    html += `<li class="page-item ${current === pages ? 'disabled' : ''}">
      <button class="page-link" data-page="${current + 1}" ${current === pages ? 'disabled' : ''}>
        &raquo;
      </button>
    </li>`;

    html += '</ul></nav>';
    paginEl.innerHTML = html;

    // Eventos de paginación
    paginEl.querySelectorAll('[data-page]').forEach(btn => {
      btn.addEventListener('click', function () {
        const p = parseInt(this.dataset.page);
        if (p >= 1 && p <= pages) doSearch(p);
      });
    });
  }

  // ─── Eventos de filtros ──────────────────────────────────────
  const inputs = form.querySelectorAll('input, select');
  inputs.forEach(input => {
    const event = input.tagName === 'SELECT' ? 'change' : 'input';
    input.addEventListener(event, function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => doSearch(1), 300);
    });
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    doSearch(1);
  });

})();
