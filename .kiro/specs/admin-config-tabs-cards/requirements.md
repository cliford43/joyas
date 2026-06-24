# Requirements Document

## Introduction

This feature reorganizes the VILUNA admin configuration page (Admin → Configuración) into Bootstrap 5 nav-tabs for better usability, and adds a new product card size configuration setting that allows the admin to control how many product cards are displayed per row on desktop viewports.

## Glossary

- **Config_Page**: The admin configuration page located at `admin/configuracion`, rendered by `ConfigController::index()`
- **Config_Model**: The `ConfigModel` class that manages configuration key-value pairs stored in the `configuracion` database table
- **Product_Card**: The reusable product card partial view (`product_card.php`) used across catalog, home, and search result pages
- **Cards_Per_Row_Setting**: The `cards_por_fila` configuration key that determines the number of product cards per row on desktop
- **Tab_Navigation**: The Bootstrap 5 nav-tabs component used to organize configuration fields into logical groups
- **Config_Controller**: The `ConfigController` class that handles loading and saving configuration data

## Requirements

### Requirement 1: Tab-Based Layout for Configuration Page

**User Story:** As an admin, I want the configuration page organized into tabs, so that I can find and manage related settings more easily without scrolling through a long form.

#### Acceptance Criteria

1. WHEN the admin navigates to the configuration page, THE Config_Page SHALL display a Bootstrap 5 nav-tabs navigation with four tabs labeled exactly: "General", "Banner Hero", "Apariencia", and "Datos bancarios"
2. WHEN the page loads, THE Config_Page SHALL display the "General" tab as active by default, showing its panel content and hiding the other three panels
3. THE Config_Page SHALL wrap all tab panels within a single HTML `<form>` element with `method="POST"` and `enctype="multipart/form-data"`
4. WHEN the admin clicks a different tab, THE Config_Page SHALL show the corresponding tab panel content and hide the previously active panel without submitting the form or reloading the page, preserving any unsaved field values across all tabs
5. WHEN the admin submits the form from any active tab, THE Config_Controller SHALL save all field values from all four tabs in a single POST request
6. IF the form submission succeeds, THEN THE Config_Controller SHALL redirect the admin back to the configuration page and display a success flash message indicating the configuration was saved
7. IF the form submission fails due to an invalid file upload, THEN THE Config_Controller SHALL redirect the admin back to the configuration page and display an error flash message indicating which upload failed

### Requirement 2: General Tab Content

**User Story:** As an admin, I want all general store information fields grouped in one tab, so that I can manage core business details in one place.

#### Acceptance Criteria

1. THE Config_Page SHALL display the following editable fields in the "General" tab, grouped under "Información de la tienda": nombre_tienda (text, max 100 characters), correo_contacto (email, max 180 characters), whatsapp (text, max 20 characters), whatsapp_mensaje (text, max 255 characters), direccion (text, max 500 characters), slogan (text, max 200 characters), metadescripcion (textarea, max 300 characters); and grouped under "Redes sociales": facebook (URL, max 255 characters) and instagram (URL, max 255 characters)
2. WHEN the admin submits the form with valid field values, THE Config_Controller SHALL persist each General tab field value to the configuracion table via Config_Model
3. WHEN the admin submits the form with empty optional fields, THE Config_Controller SHALL persist empty strings for those fields without raising an error

### Requirement 3: Banner Hero Tab Content

**User Story:** As an admin, I want the hero banner settings separated into their own tab, so that I can configure the homepage hero section without distraction.

#### Acceptance Criteria

1. THE Config_Page SHALL display the following fields in the "Banner Hero" tab: hero_activo (checkbox), hero_tagline (text input, maximum 100 characters), hero_titulo (text input, maximum 150 characters), hero_descripcion (textarea, maximum 500 characters), hero_fondo_color (color picker synchronized with a text input accepting 7-character hex format #RRGGBB), and hero_imagen (file upload with a thumbnail preview of the current image when one exists)
2. IF the hero_activo checkbox is unchecked upon form submission, THEN THE Config_Controller SHALL save the value '0' for the hero_activo key
3. WHEN a hero_imagen file of type JPEG or PNG with a size not exceeding 2 MB is uploaded, THE Config_Controller SHALL store the uploaded image and update the hero_imagen configuration value with the new file path
4. IF the uploaded hero_imagen file is not JPEG or PNG, or exceeds 2 MB, THEN THE Config_Controller SHALL reject the upload, preserve the existing hero_imagen value unchanged, and display an error message indicating the validation failure reason

### Requirement 4: Apariencia Tab Content

**User Story:** As an admin, I want appearance settings grouped together in one tab, so that I can manage all visual customizations—logo, card layout, and color palette—from a unified view.

#### Acceptance Criteria

1. THE Config_Page SHALL display the following fields in the "Apariencia" tab: logo_principal (file upload accepting JPG and PNG formats with a maximum file size of 2 MB, showing a preview of the currently saved logo when one exists), cards_por_fila (select dropdown), and all theme color pickers (theme_brand_primary, theme_brand_primary_light, theme_brand_primary_dark, theme_base_bg, theme_base_text, theme_base_muted, theme_menu_bg, theme_menu_text, theme_menu_hover, theme_btn_primary_bg, theme_btn_primary_text, theme_btn_primary_hover_bg, theme_btn_primary_hover_text, theme_btn_outline_border, theme_btn_outline_text, theme_btn_outline_hover_bg, theme_btn_outline_hover_text)
2. THE Config_Page SHALL display the cards_por_fila field as a `<select>` element with options: 2, 3, 4, and 6
3. THE Config_Page SHALL pre-select the current cards_por_fila value from the database, defaulting to 4 when no value is stored
4. THE Config_Page SHALL render each theme color picker as an input accepting only valid 6-digit hexadecimal color values (format #RRGGBB), pre-filled with the stored value or its defined default when no value is stored
5. IF the admin submits a logo_principal file that is not JPG or PNG format or exceeds 2 MB, THEN THE Config_Page SHALL reject the upload, preserve the previously saved logo unchanged, and display an error message indicating the validation failure reason

### Requirement 5: Datos Bancarios Tab Content

**User Story:** As an admin, I want banking details in a separate tab, so that I can update payment information independently.

#### Acceptance Criteria

1. THE Config_Page SHALL display the following fields in the "Datos bancarios" tab: banco_nombre (max 100 characters), banco_cuenta (max 50 characters), banco_tipo (max 30 characters), and banco_beneficiario (max 100 characters), each pre-populated with the currently stored value from the configuracion table
2. WHEN the admin saves the form, THE Config_Controller SHALL persist all four Datos bancarios field values to the configuracion table via Config_Model
3. IF the admin submits the form with all banking fields empty, THEN THE Config_Controller SHALL accept the submission and store empty values, allowing the admin to clear banking details

### Requirement 6: cards_por_fila Configuration Persistence

**User Story:** As an admin, I want the product cards-per-row setting saved to the database, so that the storefront reflects my chosen layout.

#### Acceptance Criteria

1. WHEN the admin submits the configuration form with a cards_por_fila value, THE Config_Controller SHALL validate that the value is one of: 2, 3, 4, or 6
2. IF an invalid cards_por_fila value is submitted, THEN THE Config_Controller SHALL ignore the invalid value, retain the existing stored value, and continue saving other configuration fields normally
3. WHEN a valid cards_por_fila value is submitted, THE Config_Controller SHALL persist the value to the configuracion table with key 'cards_por_fila'
4. THE Config_Controller SHALL include 'cards_por_fila' in the allowed fields array for form processing
5. IF no cards_por_fila value exists in the database (first-time setup), THEN THE Config_Controller SHALL use 4 as the default value when rendering the form

### Requirement 7: Dynamic Product Card Column Classes

**User Story:** As a customer, I want product cards to display in the layout chosen by the admin, so that I see products in an optimal grid on desktop.

#### Acceptance Criteria

1. WHEN a product card is rendered on any page (catalog, home, search results), THE Product_Card SHALL determine the column class based on the cards_por_fila value stored in the configuracion table
2. WHEN cards_por_fila is 2, THE Product_Card SHALL apply the Bootstrap class `col-6`
3. WHEN cards_por_fila is 3, THE Product_Card SHALL apply the Bootstrap classes `col-6 col-md-4`
4. WHEN cards_por_fila is 4, THE Product_Card SHALL apply the Bootstrap classes `col-6 col-md-4 col-xl-3`
5. WHEN cards_por_fila is 6, THE Product_Card SHALL apply the Bootstrap classes `col-6 col-md-4 col-lg-2`
6. IF no cards_por_fila value is stored in the database or the stored value is not one of 2, 3, 4, or 6, THEN THE Product_Card SHALL default to the classes for 4 cards per row: `col-6 col-md-4 col-xl-3`

### Requirement 8: Form Actions and Reset Functionality

**User Story:** As an admin, I want to save all configuration or reset appearance from any tab, so that form actions remain accessible regardless of which tab is active.

#### Acceptance Criteria

1. THE Config_Page SHALL display the "Guardar configuración" submit button, the "Configurar secciones del Home" link, and the "Restablecer estilo y logo" button in a persistent action bar at the bottom of the configuration form, visible regardless of which tab is active
2. WHEN the admin clicks "Restablecer estilo y logo" and confirms the browser confirmation dialog, THE Config_Controller SHALL reset all 17 theme_* color values to their predefined defaults, set logo_principal to empty, set hero_imagen to empty, and delete the previously uploaded logo and hero image files from the server
3. WHEN the reset operation completes successfully, THE Config_Controller SHALL display a success flash message and redirect the admin to the configuration page
4. WHEN the admin clicks "Guardar configuración", THE Config_Controller SHALL persist all form field values from every tab in a single submit operation regardless of which tab the admin last viewed
