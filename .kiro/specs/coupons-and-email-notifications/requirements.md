# Requirements Document

## Introduction

Este documento especifica los requisitos para dos mejoras al sistema VILUNA: (1) corrección del sistema de aplicación de cupones de descuento en el carrito para que muestre correctamente mensajes de éxito/error y actualice el resumen visualmente en tiempo real, y (2) implementación de un sistema completo de notificaciones por correo electrónico para todos los eventos importantes del sistema, incluyendo plantillas administrables y bitácora de envíos.

## Glossary

- **Sistema_Viluna**: La aplicación web de joyería Viluna, incluyendo frontend público y panel administrativo.
- **Cupón**: Un código de descuento con porcentaje, fecha de expiración y límite de usos.
- **Resumen_Carrito**: El panel lateral del carrito que muestra subtotal, descuento aplicado y total final.
- **Usuario_Autenticado**: Un usuario que ha iniciado sesión en el sistema con credenciales válidas.
- **Administrador**: Un usuario con rol "admin" que gestiona el panel administrativo.
- **Plantilla_Correo**: Una plantilla HTML con variables dinámicas utilizada para generar correos transaccionales.
- **Bitácora_Correos**: Un registro de todos los correos enviados por el sistema con su estado de entrega.
- **Estado_Pedido**: El estado actual de un pedido en su ciclo de vida (recibido, pago_pendiente, pago_revision, pago_confirmado, preparando, empaquetado, enviado, en_camino, entregado, cancelado, reembolsado).
- **Variable_Dinamica**: Un marcador en una plantilla de correo que se reemplaza con datos reales al momento del envío (ej: {nombre_cliente}, {numero_pedido}).

## Requirements

### Requirement 1

**User Story:** Como usuario autenticado, quiero que al aplicar un cupón válido el sistema me confirme visualmente el descuento, para tener certeza de que mi cupón fue aceptado antes de proceder al pago.

#### Acceptance Criteria

1. WHEN a Usuario_Autenticado submits a valid coupon code via AJAX THEN the Sistema_Viluna SHALL display a success message indicating the coupon was applied correctly.
2. WHEN a coupon is applied successfully THEN the Sistema_Viluna SHALL update the Resumen_Carrito displaying the discount amount and recalculated total without requiring a page reload.
3. WHEN a Usuario_Autenticado submits an invalid, expired, or exhausted coupon code THEN the Sistema_Viluna SHALL display a specific error message describing the reason for rejection.
4. WHEN the AJAX request for coupon application fails due to a network error THEN the Sistema_Viluna SHALL display a generic connection error message distinct from coupon validation errors.
5. WHEN a coupon is applied successfully via AJAX THEN the Sistema_Viluna SHALL show the coupon code, discount percentage, and discount amount in the Resumen_Carrito.

### Requirement 2

**User Story:** Como usuario, quiero recibir un correo de bienvenida al registrarme, para confirmar que mi cuenta fue creada exitosamente.

#### Acceptance Criteria

1. WHEN a user completes registration successfully THEN the Sistema_Viluna SHALL send a welcome email to the registered email address containing the user name, registered email, login link, and store contact information.
2. WHEN the welcome email fails to send THEN the Sistema_Viluna SHALL log the failure in the Bitácora_Correos and allow the registration to complete without interruption.

### Requirement 3

**User Story:** Como usuario, quiero recibir un correo con enlace de recuperación cuando solicito restablecer mi contraseña, para poder acceder a mi cuenta de forma segura.

#### Acceptance Criteria

1. WHEN a user requests a password reset THEN the Sistema_Viluna SHALL send an email containing a secure reset link with a time-limited token.
2. WHEN the password reset email fails to send THEN the Sistema_Viluna SHALL log the failure in the Bitácora_Correos and display an error message to the user.

### Requirement 4

**User Story:** Como usuario autenticado, quiero recibir un correo con los detalles de mi pedido al realizarlo, para tener un comprobante de la compra.

#### Acceptance Criteria

1. WHEN a pedido is created successfully THEN the Sistema_Viluna SHALL send a confirmation email containing the order number, date, list of products with quantities, shipping address, purchase total, and payment method.
2. WHEN the order confirmation email fails to send THEN the Sistema_Viluna SHALL log the failure in the Bitácora_Correos and allow the order to complete without interruption.

### Requirement 5

**User Story:** Como usuario autenticado, quiero recibir un correo cuando mi pago sea confirmado, para saber que mi pedido procederá a ser preparado.

#### Acceptance Criteria

1. WHEN an Administrador confirms a payment THEN the Sistema_Viluna SHALL send an email to the customer containing the order number, amount paid, confirmation date, and updated order status.
2. WHEN the payment confirmation email fails to send THEN the Sistema_Viluna SHALL log the failure in the Bitácora_Correos without affecting the payment confirmation process.

### Requirement 6

**User Story:** Como usuario autenticado, quiero recibir un correo cada vez que cambie el estado de mi pedido, para estar informado del progreso de mi compra.

#### Acceptance Criteria

1. WHEN an Administrador changes the Estado_Pedido THEN the Sistema_Viluna SHALL send an email to the customer containing the order number, new status, update date, administrator comments if provided, and a link to view the order.
2. WHEN the order status notification email fails to send THEN the Sistema_Viluna SHALL log the failure in the Bitácora_Correos without affecting the status change operation.

### Requirement 7

**User Story:** Como administrador, quiero recibir notificaciones por correo de eventos importantes del sistema, para estar informado de la actividad de la tienda sin necesidad de revisar el panel constantemente.

#### Acceptance Criteria

1. WHEN a new user registers THEN the Sistema_Viluna SHALL send a notification email to the Administrador email address.
2. WHEN a new order is created THEN the Sistema_Viluna SHALL send a notification email to the Administrador email address containing order details.
3. WHEN a payment receipt is uploaded THEN the Sistema_Viluna SHALL send a notification email to the Administrador email address.
4. WHEN a new review is submitted (estado pendiente) THEN the Sistema_Viluna SHALL send a notification email to the Administrador email address.

### Requirement 8

**User Story:** Como administrador, quiero gestionar las plantillas de correo electrónico del sistema, para personalizar los mensajes enviados a los clientes.

#### Acceptance Criteria

1. WHEN an Administrador accesses the email templates section THEN the Sistema_Viluna SHALL display a list of all available Plantilla_Correo types with their current subject lines.
2. WHEN an Administrador edits a Plantilla_Correo THEN the Sistema_Viluna SHALL allow modification of the subject and HTML body content.
3. WHEN an Administrador edits a Plantilla_Correo THEN the Sistema_Viluna SHALL display available Variable_Dinamica options for that template type.
4. WHEN a Plantilla_Correo is rendered for sending THEN the Sistema_Viluna SHALL replace all Variable_Dinamica markers with their corresponding real values.

### Requirement 9

**User Story:** Como administrador, quiero consultar una bitácora de todos los correos enviados, para auditar y diagnosticar problemas de entrega.

#### Acceptance Criteria

1. WHEN an Administrador accesses the email log section THEN the Sistema_Viluna SHALL display a list of all sent emails with recipient, subject, date-time, and status (enviado/error).
2. WHEN an email delivery fails THEN the Sistema_Viluna SHALL store the error message in the Bitácora_Correos record.
3. WHEN an email is sent successfully THEN the Sistema_Viluna SHALL store a record in the Bitácora_Correos with status "enviado".

