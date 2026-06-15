<?php /* Vista: Catálogo de productos VILUNA */ ?>

<!-- Breadcrumb -->
<div class="breadcrumb-viluna">
  <div class="container">
    <nav aria-label="Ruta de navegación">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url() ?>">Inicio</a></li>
        <li class="breadcrumb-item"><a href="<?= url('catalogo') ?>">Catálogo</a></li>
        <?php if (!empty($categoriaActual)): ?>
          <li class="breadcrumb-item active"><?= e($categoriaActual['nombre']) ?></li>
        <?php endif; ?>
      </ol>
    </nav>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <div class="row g-4">

      <!-- ─── Sidebar de filtros ──────────────────────── -->
      <div class="col-lg-3">
        <div class="admin-card sticky-top" style="top:90px;">
          <h2 class="h6 mb-3 text-uppercase letter-spacing" style="font-size:0.75rem;color:var(--color-gray);">
            Filtros
          </h2>

          <form id="searchForm" method="GET" action="<?= url('buscar') ?>">

            <!-- Búsqueda de texto -->
            <div class="mb-3">
              <label class="form-label-viluna" for="q">Buscar</label>
              <input type="text" id="q" name="q" class="form-control form-control-viluna"
                     placeholder="Anillo, collar, pulsera..."
                     value="<?= e($filters['q'] ?? '') ?>">
            </div>

            <!-- Categoría -->
            <div class="mb-3">
              <label class="form-label-viluna" for="categoria">Categoría</label>
              <select id="categoria" name="categoria" class="form-control form-control-viluna">
                <option value="">Todas</option>
                <?php foreach ($categorias ?? [] as $cat): ?>
                  <option value="<?= e($cat['slug']) ?>"
                    <?= ($filters['categoria'] ?? '') === $cat['slug'] ? 'selected' : '' ?>>
                    <?= e($cat['nombre']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Precio -->
            <div class="mb-3">
              <label class="form-label-viluna">Precio (S/)</label>
              <div class="d-flex gap-2">
                <input type="number" name="precio_min" class="form-control form-control-viluna"
                       placeholder="Mín." min="0" step="0.01"
                       value="<?= e($filters['precio_min'] ?? '') ?>">
                <input type="number" name="precio_max" class="form-control form-control-viluna"
                       placeholder="Máx." min="0" step="0.01"
                       value="<?= e($filters['precio_max'] ?? '') ?>">
              </div>
              <div id="searchError" class="mt-1 small" aria-live="polite"></div>
            </div>

            <!-- Ordenar -->
            <div class="mb-3">
              <label class="form-label-viluna" for="orden">Ordenar por</label>
              <select id="orden" name="orden" class="form-control form-control-viluna">
                <option value="mas_recientes"  <?= ($filters['orden'] ?? '') === 'mas_recientes'  ? 'selected' : '' ?>>Más recientes</option>
                <option value="mas_antiguos"   <?= ($filters['orden'] ?? '') === 'mas_antiguos'   ? 'selected' : '' ?>>Más antiguos</option>
                <option value="precio_asc"     <?= ($filters['orden'] ?? '') === 'precio_asc'     ? 'selected' : '' ?>>Precio: menor a mayor</option>
                <option value="precio_desc"    <?= ($filters['orden'] ?? '') === 'precio_desc'    ? 'selected' : '' ?>>Precio: mayor a menor</option>
                <option value="con_descuento"  <?= ($filters['orden'] ?? '') === 'con_descuento'  ? 'selected' : '' ?>>Con descuento</option>
              </select>
            </div>

            <!-- Descuento -->
            <div class="mb-3 form-check">
              <input type="checkbox" id="con_descuento" name="con_descuento" value="1"
                     class="form-check-input"
                     <?= !empty($filters['con_descuento']) ? 'checked' : '' ?>>
              <label class="form-check-label" for="con_descuento" style="font-size:0.85rem;">
                Solo con descuento
              </label>
            </div>

            <button type="submit" class="btn btn-gold w-100 btn-sm">Aplicar filtros</button>
            <a href="<?= url('catalogo') ?>" class="btn btn-outline-secondary w-100 btn-sm mt-2">Limpiar</a>
          </form>
        </div>
      </div>

      <!-- ─── Grid de productos ───────────────────────── -->
      <div class="col-lg-9">
        <!-- Header resultados -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <?php if (!empty($categoriaActual)): ?>
              <h1 class="h4 mb-0 font-heading"><?= e($categoriaActual['nombre']) ?></h1>
            <?php else: ?>
              <h1 class="h4 mb-0 font-heading">Catálogo</h1>
            <?php endif; ?>
            <span id="searchCount" class="small text-muted">
              <?= $total ?? 0 ?> <?= ($total ?? 0) === 1 ? 'resultado' : 'resultados' ?>
            </span>
          </div>
        </div>

        <!-- Grid -->
        <div class="row g-4" id="productsGrid">
          <?php include APP_PATH . '/views/catalog/partials/products_grid.php'; ?>
        </div>

        <!-- Paginación -->
        <div id="paginacion" class="mt-5">
          <?php if (($pages ?? 1) > 1): ?>
            <nav aria-label="Paginación de productos">
              <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= ($pages ?? 1); $i++): ?>
                  <li class="page-item <?= $i === ($currentPage ?? 1) ? 'active' : '' ?>">
                    <a class="page-link"
                       href="?<?= http_build_query(array_merge($filters ?? [], ['pagina' => $i])) ?>">
                      <?= $i ?>
                    </a>
                  </li>
                <?php endfor; ?>
              </ul>
            </nav>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</section>

<?php $extraJs = '<script src="' . asset('js/search.js') . '"></script>'; ?>
