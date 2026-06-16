# Requirements Document

## Introduction

Este documento especifica los requisitos para implementar indicadores de carga (loading) en los botones de acción del sistema Viluna Jewelry Store. El objetivo es prevenir envíos duplicados de formularios (double-click) mostrando un estado visual de carga que deshabilite el botón durante el procesamiento de la solicitud.

## Glossary

- **Sistema_Viluna**: La aplicación web de joyería Viluna, incluyendo frontend público y panel administrativo.
- **Botón_De_Acción**: Cualquier botón que dispara un envío de formulario o solicitud AJAX que modifica datos del servidor (comprar, actualizar estado, agregar al carrito, confirmar pedido, crear/editar entidades, vaciar carrito, aplicar cupón).
- **Estado_Loading**: Estado visual del botón que muestra un spinner animado y texto indicativo de procesamiento, con el botón deshabilitado para prevenir interacciones adicionales.
- **Envío_Duplicado**: Cuando un usuario hace clic múltiples veces en un botón antes de que la primera solicitud termine de procesarse.

## Requirements

### Requirement 1

**User Story:** Como usuario, quiero ver un indicador visual de carga cuando hago clic en un botón de acción, para saber que mi solicitud está siendo procesada.

#### Acceptance Criteria

1. WHEN a user clicks a Botón_De_Acción THEN the Sistema_Viluna SHALL replace the button text with a spinner animation and a loading message within 100 milliseconds.
2. WHILE a Botón_De_Acción is in Estado_Loading, THE Sistema_Viluna SHALL display the button with reduced opacity and a "not-allowed" cursor style.
3. WHEN the server response completes successfully THEN the Sistema_Viluna SHALL restore the Botón_De_Acción to its original text and enabled state.

### Requirement 2

**User Story:** Como usuario, quiero que el sistema prevenga clics duplicados en botones de acción, para evitar compras dobles o envíos repetidos.

#### Acceptance Criteria

1. WHILE a Botón_De_Acción is in Estado_Loading, THE Sistema_Viluna SHALL reject additional click events on that button.
2. WHEN a form submission is in progress THEN the Sistema_Viluna SHALL set the submit button's "disabled" attribute to true.
3. WHEN a Botón_De_Acción transitions to Estado_Loading THEN the Sistema_Viluna SHALL prevent the parent form from being submitted again via keyboard (Enter key).

### Requirement 3

**User Story:** Como usuario, quiero que el estado de carga se aplique en todos los botones de acción críticos del sitio, para tener una experiencia consistente.

#### Acceptance Criteria

1. WHEN a user submits the checkout confirmation form THEN the Sistema_Viluna SHALL apply Estado_Loading to the "Confirmar pedido" button.
2. WHEN a user clicks "Agregar al carrito" on a product page THEN the Sistema_Viluna SHALL apply Estado_Loading to that button.
3. WHEN a user clicks "Vaciar carrito" THEN the Sistema_Viluna SHALL apply Estado_Loading to that button.
4. WHEN a user clicks "Aplicar" cupón THEN the Sistema_Viluna SHALL apply Estado_Loading to that button.
5. WHEN an administrator clicks "Actualizar estado" on an order detail page THEN the Sistema_Viluna SHALL apply Estado_Loading to that button.
6. WHEN an administrator submits a product or category create/edit form THEN the Sistema_Viluna SHALL apply Estado_Loading to the submit button.

### Requirement 4

**User Story:** Como usuario, quiero que si una solicitud falla, el botón vuelva a su estado original, para poder reintentar la acción.

#### Acceptance Criteria

1. IF a server request fails or times out THEN the Sistema_Viluna SHALL restore the Botón_De_Acción to its original enabled state and text.
2. IF a form submission produces a validation error on the server (page reload with errors) THEN the Sistema_Viluna SHALL render the Botón_De_Acción in its original enabled state on the new page load.
3. IF a network error occurs during an AJAX request THEN the Sistema_Viluna SHALL restore the Botón_De_Acción within 10 seconds maximum (timeout fallback).
