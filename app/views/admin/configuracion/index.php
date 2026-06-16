<?php /* Admin: Panel de configuración general */ ?>
<div class="admin-page-header">
  <h1>Configuración general</h1>
</div>
<div class="admin-card admin-form" style="max-width:720px;">
  <form method="POST" enctype="multipart/form-data" action="<?= url('admin/configuracion') ?>">
    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

    <h2 class="h6 text-uppercase mb-3" style="color:var(--color-gold);letter-spacing:2px;">Branding</h2>
    <div class="row g-3 mb-4">
      <div class="col-12">
        <label class="form-label">Logo principal (sitio y admin)</label>
        <input type="file" name="logo_principal" class="form-control" accept=".jpg,.jpeg,.png">
        <div class="form-text">Formato JPG/PNG, máximo 2 MB.</div>
      </div>
      <?php if (!empty($config['logo_principal'])): ?>
        <div class="col-12">
          <img src="<?= e(mediaUrl((string)$config['logo_principal'])) ?>" alt="Logo actual"
               style="max-height:64px;max-width:260px;object-fit:contain;border:1px solid rgba(0,0,0,.1);padding:.4rem;background:#fff;">
        </div>
      <?php endif; ?>
    </div>

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

    <h2 class="h6 text-uppercase mb-3" style="color:var(--color-gold);letter-spacing:2px;">Texto del Hero (Página principal)</h2>
    <div class="row g-3 mb-4">
      <div class="col-12">
        <label class="form-label">Tagline</label>
        <input type="text" name="hero_tagline" class="form-control" placeholder="Joyería fina desde 2016" value="<?= e($config['hero_tagline'] ?? '') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Título principal</label>
        <input type="text" name="hero_titulo" class="form-control" placeholder="Elegancia que perdura para siempre" value="<?= e($config['hero_titulo'] ?? '') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Descripción</label>
        <textarea name="hero_descripcion" class="form-control" rows="2" placeholder="Descubre nuestra colección exclusiva..."><?= e($config['hero_descripcion'] ?? '') ?></textarea>
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

    <h2 class="h6 text-uppercase mb-3" style="color:var(--color-gold);letter-spacing:2px;">Paleta General</h2>
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <label class="form-label">Marca principal</label>
        <input type="color" name="theme_brand_primary" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_brand_primary'] ?? '', '#D4AF37')) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Marca clara</label>
        <input type="color" name="theme_brand_primary_light" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_brand_primary_light'] ?? '', '#F5D87A')) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Marca oscura</label>
        <input type="color" name="theme_brand_primary_dark" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_brand_primary_dark'] ?? '', '#B8961E')) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Fondo principal</label>
        <input type="color" name="theme_base_bg" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_base_bg'] ?? '', '#FFFFFF')) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Texto principal</label>
        <input type="color" name="theme_base_text" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_base_text'] ?? '', '#111111')) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Texto secundario</label>
        <input type="color" name="theme_base_muted" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_base_muted'] ?? '', '#6C757D')) ?>">
      </div>
    </div>

    <h2 class="h6 text-uppercase mb-3" style="color:var(--color-gold);letter-spacing:2px;">Menús</h2>
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <label class="form-label">Fondo menú</label>
        <input type="color" name="theme_menu_bg" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_menu_bg'] ?? '', '#111111')) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Texto menú</label>
        <input type="color" name="theme_menu_text" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_menu_text'] ?? '', '#FFFFFF')) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Hover/activo menú</label>
        <input type="color" name="theme_menu_hover" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_menu_hover'] ?? '', '#D4AF37')) ?>">
      </div>
    </div>

    <h2 class="h6 text-uppercase mb-3" style="color:var(--color-gold);letter-spacing:2px;">Botones</h2>
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <label class="form-label">Primario fondo</label>
        <input type="color" name="theme_btn_primary_bg" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_btn_primary_bg'] ?? '', '#D4AF37')) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Primario texto</label>
        <input type="color" name="theme_btn_primary_text" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_btn_primary_text'] ?? '', '#111111')) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Primario hover</label>
        <input type="color" name="theme_btn_primary_hover_bg" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_btn_primary_hover_bg'] ?? '', '#B8961E')) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Texto hover</label>
        <input type="color" name="theme_btn_primary_hover_text" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_btn_primary_hover_text'] ?? '', '#FFFFFF')) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Outline borde</label>
        <input type="color" name="theme_btn_outline_border" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_btn_outline_border'] ?? '', '#D4AF37')) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Outline texto</label>
        <input type="color" name="theme_btn_outline_text" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_btn_outline_text'] ?? '', '#D4AF37')) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Outline hover</label>
        <input type="color" name="theme_btn_outline_hover_bg" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_btn_outline_hover_bg'] ?? '', '#D4AF37')) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Outline texto hover</label>
        <input type="color" name="theme_btn_outline_hover_text" class="form-control form-control-color"
               value="<?= e(normalizeHexColor($config['theme_btn_outline_hover_text'] ?? '', '#111111')) ?>">
      </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <button type="submit" class="btn btn-gold">Guardar configuración</button>
      <a href="<?= url('admin/configuracion/home') ?>" class="btn btn-outline-gold">
        <i class="bi bi-house me-1"></i>Configurar secciones del Home
      </a>
      <button type="submit" name="reset_theme" value="1" class="btn btn-outline-secondary"
              onclick="return confirm('¿Restablecer paleta y logo a los valores por defecto?');">
        Restablecer estilo y logo
      </button>
    </div>
  </form>
</div>
