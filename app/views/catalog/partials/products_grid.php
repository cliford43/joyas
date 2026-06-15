<?php
/**
 * Partial: Grid/Lista de productos para catálogo y respuesta AJAX del buscador.
 * Requiere: $productos (array), $csrfToken (string)
 */
?>
<?php if (empty($productos)): ?>
  <div class="col-12 text-center py-5">
    <i class="bi bi-search" style="font-size:3rem;color:var(--color-gold);opacity:0.4;"></i>
    <p class="mt-3" style="color:var(--color-gray);">No se encontraron productos con los filtros seleccionados.</p>
    <a href="<?= url('catalogo') ?>" class="btn btn-outline-gold btn-sm mt-2">Ver todo el catálogo</a>
  </div>
<?php else: ?>
  <?php foreach ($productos as $prod): ?>
    <?php include APP_PATH . '/views/partials/product_card.php'; ?>
  <?php endforeach; ?>
<?php endif; ?>
