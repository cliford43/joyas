<?php /* Admin: Editar comentario de reseña */ ?>
<div class="admin-page-header">
  <h1>Editar Reseña #<?= (int)$resena['id'] ?></h1>
  <a href="<?= url('admin/resenas') ?>" class="btn btn-sm btn-outline-secondary">← Volver</a>
</div>

<div class="admin-card">
  <div class="mb-3">
    <strong>Producto:</strong> <?= e($resena['producto_nombre'] ?? 'N/A') ?><br>
    <strong>Usuario:</strong> <?= e(($resena['usuario_nombre'] ?? '') . ' ' . ($resena['usuario_apellido'] ?? '')) ?><br>
    <strong>Calificación:</strong>
    <?php for ($i = 1; $i <= 5; $i++): ?>
      <i class="bi bi-star<?= $i <= (int)$resena['calificacion'] ? '-fill text-warning' : '' ?>"></i>
    <?php endfor; ?>
    <br>
    <strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($resena['fecha_creacion'])) ?><br>
    <strong>Estado:</strong> <span class="status-badge status-<?= e($resena['estado']) ?>"><?= e(ucfirst($resena['estado'])) ?></span>
  </div>

  <form method="POST" action="<?= url('admin/resenas/' . $resena['id'] . '/editar') ?>">
    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

    <div class="mb-3">
      <label for="comentario" class="form-label">Comentario</label>
      <textarea name="comentario" id="comentario" class="form-control" rows="5" minlength="10" maxlength="1000" required><?= e($resena['comentario']) ?></textarea>
      <div class="form-text">Entre 10 y 1000 caracteres.</div>
    </div>

    <button type="submit" class="btn btn-gold">Guardar cambios</button>
    <a href="<?= url('admin/resenas') ?>" class="btn btn-outline-secondary">Cancelar</a>
  </form>
</div>
