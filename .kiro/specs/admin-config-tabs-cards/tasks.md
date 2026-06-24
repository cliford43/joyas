# Implementation Plan: Admin Config Tabs & Cards Per Row

## Overview

Restructure the admin configuration page into Bootstrap 5 nav-tabs (General, Banner Hero, Apariencia, Datos bancarios) and add a `cards_por_fila` setting that dynamically controls product card column classes across the storefront. All changes stay within the existing MVC structure — no new routes, controllers, or models needed.

## Tasks

- [x] 1. Add cardColumnClass() helper function
  - [x] 1.1 Add the `cardColumnClass()` helper to `core/helpers.php`
    - Add a new function wrapped in `if (!function_exists('cardColumnClass'))` guard
    - Use a `match` expression to map '2' → 'col-6', '3' → 'col-6 col-md-4', '4' → 'col-6 col-md-4 col-xl-3', '6' → 'col-6 col-md-4 col-lg-2'
    - Default case returns 'col-6 col-md-4 col-xl-3' for any invalid input
    - Accept `string|int` parameter with default value of 4
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6_

  - [ ]* 1.2 Write property test for cardColumnClass()
    - **Property 2: cardColumnClass fallback — invalid inputs always produce default classes**
    - **Validates: Requirements 7.1, 7.6**

- [x] 2. Modify ConfigController to handle cards_por_fila
  - [x] 2.1 Update `ConfigController::update()` in `app/controllers/Admin/ConfigController.php`
    - Add `'cards_por_fila'` to the `$allowed` array
    - After building `$data` from `$allowed`, add validation: if `$data['cards_por_fila']` is not in `['2', '3', '4', '6']`, unset it from `$data` so the invalid value is never persisted
    - Existing hero_activo checkbox handling and file upload logic remain unchanged
    - _Requirements: 6.1, 6.2, 6.3, 6.4_

  - [ ]* 2.2 Write property test for cards_por_fila validation
    - **Property 1: cards_por_fila validation — only valid values are persisted**
    - **Validates: Requirements 6.1, 6.2, 6.3**

- [x] 3. Checkpoint - Verify backend logic
  - Ensure all tests pass, ask the user if questions arise.

- [x] 4. Rewrite admin configuration view with Bootstrap 5 nav-tabs
  - [x] 4.1 Rewrite `app/views/admin/configuracion/index.php` with tab structure
    - Wrap all content in a single `<form method="POST" enctype="multipart/form-data">`
    - Add Bootstrap 5 nav-tabs `<ul class="nav nav-tabs">` with four `<li>` items: "General" (active), "Banner Hero", "Apariencia", "Datos bancarios"
    - Use `data-bs-toggle="tab"` attributes and `href` pointing to tab pane IDs: `#tab-general`, `#tab-hero`, `#tab-apariencia`, `#tab-bancarios`
    - Add `role="tablist"` to `<ul>` and `role="tabpanel"` to each `.tab-pane`
    - First tab pane gets classes `tab-pane fade show active`
    - _Requirements: 1.1, 1.2, 1.3, 1.4_

  - [x] 4.2 Implement "General" tab content
    - Group fields under "Información de la tienda": nombre_tienda, correo_contacto, whatsapp, whatsapp_mensaje, direccion, slogan, metadescripcion
    - Group fields under "Redes sociales": facebook, instagram
    - Pre-populate all fields from `$config` array with `e()` escaping
    - Apply `maxlength` attributes per requirements (nombre_tienda: 100, correo_contacto: 180, whatsapp: 20, whatsapp_mensaje: 255, direccion: 500, slogan: 200, metadescripcion: 300, facebook: 255, instagram: 255)
    - _Requirements: 2.1, 2.2, 2.3_

  - [x] 4.3 Implement "Banner Hero" tab content
    - Include hero_activo checkbox, hero_tagline, hero_titulo, hero_descripcion text fields
    - Include hero_fondo_color as color picker synced with hex text input (keep existing JS sync logic)
    - Include hero_imagen file upload with thumbnail preview of current image
    - Apply maxlength: hero_tagline 100, hero_titulo 150, hero_descripcion 500
    - _Requirements: 3.1, 3.2, 3.3, 3.4_

  - [x] 4.4 Implement "Apariencia" tab content
    - Include logo_principal file upload with preview of current logo
    - Include `cards_por_fila` as `<select>` with options 2, 3, 4, 6; pre-select current value defaulting to '4'
    - Include all 17 theme color pickers (theme_brand_primary, theme_brand_primary_light, theme_brand_primary_dark, theme_base_bg, theme_base_text, theme_base_muted, theme_menu_bg, theme_menu_text, theme_menu_hover, theme_btn_primary_bg, theme_btn_primary_text, theme_btn_primary_hover_bg, theme_btn_primary_hover_text, theme_btn_outline_border, theme_btn_outline_text, theme_btn_outline_hover_bg, theme_btn_outline_hover_text)
    - Each color picker pre-filled using `normalizeHexColor()` with appropriate defaults
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [x] 4.5 Implement "Datos bancarios" tab content
    - Include banco_nombre, banco_cuenta, banco_tipo, banco_beneficiario fields
    - Pre-populate from `$config` with `e()` escaping
    - Apply maxlength: banco_nombre 100, banco_cuenta 50, banco_tipo 30, banco_beneficiario 100
    - _Requirements: 5.1, 5.2, 5.3_

  - [x] 4.6 Add persistent action bar below tabs
    - Place outside tab-content but inside `<form>`, always visible
    - Include "Guardar configuración" submit button (class `btn btn-gold`)
    - Include "Configurar secciones del Home" link to `admin/configuracion/home` (class `btn btn-outline-gold`)
    - Include "Restablecer estilo y logo" button with `name="reset_theme" value="1"` and `onclick` confirm dialog
    - Wrap in a `<div class="config-action-bar">`
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 1.5, 1.6, 1.7_

- [x] 5. Modify product_card.php to use dynamic column classes
  - [x] 5.1 Update `app/views/partials/product_card.php` to use `cardColumnClass()`
    - Replace the hardcoded `<div class="col-6 col-md-4 col-xl-3">` with dynamic class
    - Add `$colClass = cardColumnClass(\App\Models\ConfigModel::get('cards_por_fila', '4'));` at the top of the partial
    - Change the wrapper div to `<div class="<?= $colClass ?>">`
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6_

- [x] 6. Add config action bar CSS to admin.css
  - [x] 6.1 Append `.config-action-bar` styles to `public/assets/css/admin.css`
    - Add flex layout with gap, wrap, top padding, top margin, and border-top
    - Style: `display: flex; gap: 0.5rem; flex-wrap: wrap; padding: 1.25rem 0 0; margin-top: 1.5rem; border-top: 1px solid #dee2e6;`
    - _Requirements: 8.1_

- [x] 7. Final checkpoint - Ensure all changes work together
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Property tests validate universal correctness properties from the design document
- No test framework is currently installed; optional PBT tasks assume future PHPUnit + Eris setup
- No database migration required — the `configuracion` table already supports arbitrary key-value pairs
- Bootstrap 5 tab JS is already included in the admin layout's Bootstrap bundle

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "6.1"] },
    { "id": 1, "tasks": ["1.2", "2.1"] },
    { "id": 2, "tasks": ["2.2", "4.1"] },
    { "id": 3, "tasks": ["4.2", "4.3", "4.4", "4.5"] },
    { "id": 4, "tasks": ["4.6", "5.1"] }
  ]
}
```
