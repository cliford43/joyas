<?php /* Vista: Wishlist del cliente */ ?>
<div class="container py-5">
  <div class="row g-4">
    <div class="col-lg-3"><?php include APP_PATH . '/views/client/partials/sidebar.php'; ?></div>
    <div class="col-lg-9">
      <h1 class="font-heading mb-4">Mi lista de deseos
        <span class="small text-gold" style="font-size:1rem;">(<?= count($productos ?? []) ?>)</span>
      </h1>

      <?php if (empty($productos)): ?>
        <div class="text-center py-5">
          <i class="bi bi-heart" style="font-size:3rem;color:rgba(212,175,55,0.3);"></i>
          <p class="mt-3 text-muted">Tu lista de deseos está vacía.</p>
          <a href="<?= url('catalogo') ?>" class="btn btn-gold mt-2">Descubrir joyas</a>
        </div>
      <?php else: ?>
        <div class="row g-4">
          <?php foreach ($productos as $prod): ?>
            <?php
            $cardSection = 'wishlist';
            include APP_PATH . '/views/partials/product_card.php';
            ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
