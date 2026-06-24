<?php /* Admin: Panel de configuración general */ ?>
<div class="admin-page-header">
  <h1>Configuración general</h1>
</div>
<div class="admin-card admin-form">
  <form method="POST" enctype="multipart/form-data" action="<?= url('admin/configuracion') ?>">
    <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="configTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#tab-general" role="tab" aria-controls="tab-general" aria-selected="true">General</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link" id="hero-tab" data-bs-toggle="tab" href="#tab-hero" role="tab" aria-controls="tab-hero" aria-selected="false">Banner Hero</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link" id="apariencia-tab" data-bs-toggle="tab" href="#tab-apariencia" role="tab" aria-controls="tab-apariencia" aria-selected="false">Apariencia</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link" id="bancarios-tab" data-bs-toggle="tab" href="#tab-bancarios" role="tab" aria-controls="tab-bancarios" aria-selected="false">Datos bancarios</a>
      </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content pt-4">

      <!-- Tab: General -->
      <div class="tab-pane fade show active" id="tab-general" role="tabpanel" aria-labelledby="general-tab">
        <h2 class="h6 text-uppercase mb-3" style="color:var(--color-gold);letter-spacing:2px;">Información de la tienda</h2>
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label">Nombre de la tienda</label>
            <input type="text" name="nombre_tienda" class="form-control" maxlength="100" value="<?= e($config['nombre_tienda'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Correo de contacto</label>
            <input type="email" name="correo_contacto" class="form-control" maxlength="180" value="<?= e($config['correo_contacto'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Teléfono WhatsApp</label>
            <input type="text" name="whatsapp" class="form-control" maxlength="20" placeholder="50200000000" value="<?= e($config['whatsapp'] ?? '') ?>">
            <div class="form-text">Formato: código de país + número. Ej: 50212345678</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Mensaje predeterminado WhatsApp</label>
            <input type="text" name="whatsapp_mensaje" class="form-control" maxlength="255" value="<?= e($config['whatsapp_mensaje'] ?? '') ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Dirección física</label>
            <input type="text" name="direccion" class="form-control" maxlength="500" value="<?= e($config['direccion'] ?? '') ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Slogan</label>
            <input type="text" name="slogan" class="form-control" maxlength="200" value="<?= e($config['slogan'] ?? '') ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Meta descripción (SEO)</label>
            <textarea name="metadescripcion" class="form-control" rows="2" maxlength="300"><?= e($config['metadescripcion'] ?? '') ?></textarea>
          </div>
        </div>

        <h2 class="h6 text-uppercase mb-3" style="color:var(--color-gold);letter-spacing:2px;">Redes sociales</h2>
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label">Facebook</label>
            <input type="url" name="facebook" class="form-control" maxlength="255" value="<?= e($config['facebook'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Instagram</label>
            <input type="url" name="instagram" class="form-control" maxlength="255" value="<?= e($config['instagram'] ?? '') ?>">
          </div>
        </div>
      </div>

      <!-- Tab: Banner Hero -->
      <div class="tab-pane fade" id="tab-hero" role="tabpanel" aria-labelledby="hero-tab">
        <div class="row g-3 mb-4">
          <div class="col-12">
            <div class="form-check mb-3">
              <input type="checkbox" name="hero_activo" id="hero_activo" class="form-check-input" value="1"
                     <?= ($config['hero_activo'] ?? '1') === '1' ? 'checked' : '' ?>>
              <label class="form-check-label" for="hero_activo">
                <strong>Mostrar banner hero</strong> (tagline, título, descripción e imagen)
              </label>
            </div>
          </div>
          <div class="col-12">
            <label class="form-label">Tagline</label>
            <input type="text" name="hero_tagline" class="form-control" maxlength="100" placeholder="Joyería fina desde 2016" value="<?= e($config['hero_tagline'] ?? '') ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Título principal</label>
            <input type="text" name="hero_titulo" class="form-control" maxlength="150" placeholder="Elegancia que perdura para siempre" value="<?= e($config['hero_titulo'] ?? '') ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Descripción</label>
            <textarea name="hero_descripcion" class="form-control" rows="2" maxlength="500" placeholder="Descubre nuestra colección exclusiva..."><?= e($config['hero_descripcion'] ?? '') ?></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Color de fondo del banner hero</label>
            <div class="d-flex align-items-center gap-2">
              <input type="color" name="hero_fondo_color" class="form-control form-control-color"
                     value="<?= e($config['hero_fondo_color'] ?? '#111111') ?>"
                     style="width:50px;height:38px;">
              <input type="text" name="hero_fondo_color_text" class="form-control" style="max-width:120px;"
                     value="<?= e($config['hero_fondo_color'] ?? '#111111') ?>"
                     placeholder="#111111" pattern="^#[0-9A-Fa-f]{6}$">
              <div class="form-text">Hex color del fondo del banner principal.</div>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Imagen junto al texto del hero</label>
            <?php if (!empty($config['hero_imagen'])): ?>
              <div class="mb-2">
                <img src="/<?= e(ltrim($config['hero_imagen'], '/')) ?>" alt="Hero actual"
                     style="max-height:120px;border:var(--border-gold);border-radius:4px;">
              </div>
            <?php endif; ?>
            <input type="file" name="hero_imagen" class="form-control" accept=".jpg,.jpeg,.png">
            <div class="form-text">JPG o PNG, máximo 2 MB.</div>
          </div>
        </div>
      </div>

      <!-- Tab: Apariencia -->
      <div class="tab-pane fade" id="tab-apariencia" role="tabpanel" aria-labelledby="apariencia-tab">
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

        <h2 class="h6 text-uppercase mb-3" style="color:var(--color-gold);letter-spacing:2px;">Tarjetas de producto</h2>
        <p class="text-muted small mb-3">Las tarjetas del Home se configuran por sección en "Configurar secciones del Home".</p>
        <div class="row g-3 mb-4">
          <div class="col-md-6 col-lg-4">
            <label class="form-label">Catálogo</label>
            <select name="cards_por_fila_catalogo" class="form-select">
              <option value="2" <?= ($config['cards_por_fila_catalogo'] ?? $config['cards_por_fila'] ?? '4') === '2' ? 'selected' : '' ?>>2 por fila</option>
              <option value="3" <?= ($config['cards_por_fila_catalogo'] ?? $config['cards_por_fila'] ?? '4') === '3' ? 'selected' : '' ?>>3 por fila</option>
              <option value="4" <?= ($config['cards_por_fila_catalogo'] ?? $config['cards_por_fila'] ?? '4') === '4' ? 'selected' : '' ?>>4 por fila</option>
              <option value="6" <?= ($config['cards_por_fila_catalogo'] ?? $config['cards_por_fila'] ?? '4') === '6' ? 'selected' : '' ?>>6 por fila</option>
            </select>
          </div>
          <div class="col-md-6 col-lg-4">
            <label class="form-label">Búsqueda</label>
            <select name="cards_por_fila_busqueda" class="form-select">
              <option value="2" <?= ($config['cards_por_fila_busqueda'] ?? $config['cards_por_fila'] ?? '4') === '2' ? 'selected' : '' ?>>2 por fila</option>
              <option value="3" <?= ($config['cards_por_fila_busqueda'] ?? $config['cards_por_fila'] ?? '4') === '3' ? 'selected' : '' ?>>3 por fila</option>
              <option value="4" <?= ($config['cards_por_fila_busqueda'] ?? $config['cards_por_fila'] ?? '4') === '4' ? 'selected' : '' ?>>4 por fila</option>
              <option value="6" <?= ($config['cards_por_fila_busqueda'] ?? $config['cards_por_fila'] ?? '4') === '6' ? 'selected' : '' ?>>6 por fila</option>
            </select>
          </div>
          <div class="col-md-6 col-lg-4">
            <label class="form-label">Wishlist</label>
            <select name="cards_por_fila_wishlist" class="form-select">
              <option value="2" <?= ($config['cards_por_fila_wishlist'] ?? $config['cards_por_fila'] ?? '4') === '2' ? 'selected' : '' ?>>2 por fila</option>
              <option value="3" <?= ($config['cards_por_fila_wishlist'] ?? $config['cards_por_fila'] ?? '4') === '3' ? 'selected' : '' ?>>3 por fila</option>
              <option value="4" <?= ($config['cards_por_fila_wishlist'] ?? $config['cards_por_fila'] ?? '4') === '4' ? 'selected' : '' ?>>4 por fila</option>
              <option value="6" <?= ($config['cards_por_fila_wishlist'] ?? $config['cards_por_fila'] ?? '4') === '6' ? 'selected' : '' ?>>6 por fila</option>
            </select>
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
      </div>

      <!-- Tab: Datos bancarios -->
      <div class="tab-pane fade" id="tab-bancarios" role="tabpanel" aria-labelledby="bancarios-tab">
        <h2 class="h6 text-uppercase mb-3" style="color:var(--color-gold);letter-spacing:2px;">Datos bancarios (transferencia)</h2>
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label">Banco</label>
            <input type="text" name="banco_nombre" class="form-control" maxlength="100" value="<?= e($config['banco_nombre'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Número de cuenta</label>
            <input type="text" name="banco_cuenta" class="form-control" maxlength="50" value="<?= e($config['banco_cuenta'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Tipo de cuenta</label>
            <input type="text" name="banco_tipo" class="form-control" maxlength="30" placeholder="Monetaria / Ahorro" value="<?= e($config['banco_tipo'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Nombre del beneficiario</label>
            <input type="text" name="banco_beneficiario" class="form-control" maxlength="100" value="<?= e($config['banco_beneficiario'] ?? '') ?>">
          </div>
        </div>
      </div>

    </div><!-- /.tab-content -->

    <!-- Persistent action bar -->
    <div class="config-action-bar">
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

<script>
// Sincronizar input color con input texto del hero fondo
(function(){
  const colorInput = document.querySelector('[name="hero_fondo_color"]');
  const textInput = document.querySelector('[name="hero_fondo_color_text"]');
  if(!colorInput||!textInput) return;
  colorInput.addEventListener('input', function(){ textInput.value = this.value; });
  textInput.addEventListener('input', function(){
    if(/^#[0-9A-Fa-f]{6}$/.test(this.value)) colorInput.value = this.value;
  });
  // Al enviar, copiar el valor del color picker al campo real
  colorInput.closest('form').addEventListener('submit', function(){
    if(textInput.value && /^#[0-9A-Fa-f]{6}$/.test(textInput.value)){
      colorInput.value = textInput.value;
    }
  });
})();
</script>
