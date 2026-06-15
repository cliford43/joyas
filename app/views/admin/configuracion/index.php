<?php /* Admin: Panel de configuración general */ ?>
<div class="admin-page-header">
  <h1>Configuración general</h1>
</div>
<div class="admin-card admin-form" style="max-width:720px;">
  <form method="POST" action="<?= url('admin/configuracion') ?>">
    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

    <h2 class="h6 text-uppercase mb-3" style="color:var(--color-gold);letter-spacing:2px;">Información de la tienda</h2>
    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <label class="form-label">Nombre de la tienda</label>
        <input type="text" name="nombre_tienda" class="form-control" value="<?= e($config['nombre_tienda'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Correo de contacto</label>
        <input type="email" name="correo_contacto" class="form-control" value="<?= e($config['correo_contacto'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Teléfono WhatsApp</label>
        <input type="text" name="whatsapp" class="form-control" placeholder="50200000000" value="<?= e($config['whatsapp'] ?? '') ?>">
        <div class="form-text">Formato: código de país + número. Ej: 50212345678</div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Mensaje predeterminado WhatsApp</label>
        <input type="text" name="whatsapp_mensaje" class="form-control" value="<?= e($config['whatsapp_mensaje'] ?? '') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Dirección física</label>
        <input type="text" name="direccion" class="form-control" value="<?= e($config['direccion'] ?? '') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Slogan</label>
        <input type="text" name="slogan" class="form-control" value="<?= e($config['slogan'] ?? '') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Meta descripción (SEO)</label>
        <textarea name="metadescripcion" class="form-control" rows="2"><?= e($config['metadescripcion'] ?? '') ?></textarea>
      </div>
    </div>

    <h2 class="h6 text-uppercase mb-3" style="color:var(--color-gold);letter-spacing:2px;">Redes sociales</h2>
    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <label class="form-label">Facebook</label>
        <input type="url" name="facebook" class="form-control" value="<?= e($config['facebook'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Instagram</label>
        <input type="url" name="instagram" class="form-control" value="<?= e($config['instagram'] ?? '') ?>">
      </div>
    </div>

    <h2 class="h6 text-uppercase mb-3" style="color:var(--color-gold);letter-spacing:2px;">Datos bancarios (transferencia)</h2>
    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <label class="form-label">Banco</label>
        <input type="text" name="banco_nombre" class="form-control" value="<?= e($config['banco_nombre'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Número de cuenta</label>
        <input type="text" name="banco_cuenta" class="form-control" value="<?= e($config['banco_cuenta'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Tipo de cuenta</label>
        <input type="text" name="banco_tipo" class="form-control" placeholder="Monetaria / Ahorro" value="<?= e($config['banco_tipo'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Nombre del beneficiario</label>
        <input type="text" name="banco_beneficiario" class="form-control" value="<?= e($config['banco_beneficiario'] ?? '') ?>">
      </div>
    </div>

    <button type="submit" class="btn btn-gold">Guardar configuración</button>
  </form>
</div>
