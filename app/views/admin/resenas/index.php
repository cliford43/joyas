<?php /* Admin: Gestión de reseñas */ ?>
<div class="admin-page-header">
  <h1>Reseñas</h1>
  <span class="text-muted"><?= (int)$total ?> resultado(s)</span>
</div>

<!-- Filtros -->
<div class="admin-card mb-3">
  <form method="GET" action="<?= url('admin/resenas') ?>" class="row g-2 align-items-end">
    <!-- Producto -->
    <div class="col-md-2">
      <label class="form-label form-label-sm">Producto</label>
      <select name="producto_id" class="form-select form-select-sm">
        <option value="">Todos</option>
        <?php foreach ($productos as $p): ?>
          <option value="<?= (int)$p['id'] ?>" <?= (($filters['producto_id'] ?? '') == $p['id']) ? 'selected' : '' ?>>
            <?= e($p['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <!-- Estado -->
    <div class="col-md-2">
      <label class="form-label form-label-sm">Estado</label>
      <select name="estado" class="form-select form-select-sm">
        <option value="">Todos</option>
        <option value="pendiente" <?= (($filters['estado'] ?? '') === 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
        <option value="aprobado" <?= (($filters['estado'] ?? '') === 'aprobado') ? 'selected' : '' ?>>Aprobado</option>
        <option value="rechazado" <?= (($filters['estado'] ?? '') === 'rechazado') ? 'selected' : '' ?>>Rechazado</option>
      </select>
    </div>
    <!-- Calificación -->
    <div class="col-md-2">
      <label class="form-label form-label-sm">Calificación</label>
      <select name="calificacion" class="form-select form-select-sm">
        <option value="">Todas</option>
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <option value="<?= $i ?>" <?= (($filters['calificacion'] ?? '') == $i) ? 'selected' : '' ?>><?= $i ?> ★</option>
        <?php endfor; ?>
      </select>
    </div>
    <!-- Fecha desde -->
    <div class="col-md-2">
      <label class="form-label form-label-sm">Desde</label>
      <input type="date" name="fecha_desde" class="form-control form-control-sm" value="<?= e($filters['fecha_desde'] ?? '') ?>">
    </div>
    <!-- Fecha hasta -->
    <div class="col-md-2">
      <label class="form-label form-label-sm">Hasta</label>
      <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="<?= e($filters['fecha_hasta'] ?? '') ?>">
    </div>
    <!-- Botones -->
    <div class="col-md-2 d-flex gap-1">
      <button type="submit" class="btn btn-sm btn-gold">Filtrar</button>
      <a href="<?= url('admin/resenas') ?>" class="btn btn-sm btn-outline-secondary">Limpiar</a>
    </div>
  </form>
</div>

<!-- Tabla de reseñas -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="table admin-table mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Producto</th>
          <th>Usuario</th>
          <th>Calificación</th>
          <th>Comentario</th>
          <th>Estado</th>
          <th>Fecha</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($resenas as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= e($r['producto_nombre'] ?? '') ?></td>
          <td><?= e(($r['usuario_nombre'] ?? '') . ' ' . ($r['usuario_apellido'] ?? '')) ?></td>
          <td>
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <i class="bi bi-star<?= $i <= (int)$r['calificacion'] ? '-fill text-warning' : '' ?>"></i>
            <?php endfor; ?>
          </td>
          <td title="<?= e($r['comentario']) ?>"><?= e(mb_substr($r['comentario'], 0, 60)) ?><?= mb_strlen($r['comentario']) > 60 ? '...' : '' ?></td>
          <td><span class="status-badge status-<?= e($r['estado']) ?>"><?= e(ucfirst($r['estado'])) ?></span></td>
          <td><?= date('d/m/Y', strtotime($r['fecha_creacion'])) ?></td>
          <td>
            <div class="d-flex gap-1 flex-wrap">
              <?php if ($r['estado'] !== 'aprobado'): ?>
                <form method="POST" action="<?= url('admin/resenas/' . $r['id'] . '/aprobar') ?>" style="display:inline;">
                  <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                  <button type="submit" class="btn btn-sm btn-outline-success" title="Aprobar"><i class="bi bi-check-lg"></i></button>
                </form>
              <?php endif; ?>
              <?php if ($r['estado'] !== 'rechazado'): ?>
                <form method="POST" action="<?= url('admin/resenas/' . $r['id'] . '/rechazar') ?>" style="display:inline;">
                  <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                  <button type="submit" class="btn btn-sm btn-outline-warning" title="Rechazar"><i class="bi bi-x-lg"></i></button>
                </form>
              <?php endif; ?>
              <a href="<?= url('admin/resenas/' . $r['id'] . '/editar') ?>" class="btn btn-sm btn-outline-secondary" title="Editar"><i class="bi bi-pencil"></i></a>
              <form method="POST" action="<?= url('admin/resenas/' . $r['id'] . '/eliminar') ?>" style="display:inline;" onsubmit="return confirm('¿Eliminar esta reseña permanentemente?');">
                <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($resenas)): ?>
          <tr><td colspan="8" class="text-center text-muted py-4">No hay reseñas con estos filtros.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Paginación -->
  <?php if ($totalPages > 1): ?>
  <nav class="mt-3" aria-label="Paginación de reseñas">
    <ul class="pagination pagination-sm justify-content-center mb-0">
      <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <?php
          $queryParams = $filters;
          $queryParams['page'] = $p;
          $qs = http_build_query($queryParams);
        ?>
        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
          <a class="page-link" href="<?= url('admin/resenas?' . $qs) ?>"><?= $p ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <?php endif; ?>
</div>
