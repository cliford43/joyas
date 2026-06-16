-- ============================================================
-- VILUNA — Migración: Tablas para sistema de correo electrónico
-- plantillas_correo + correos_log
-- ============================================================

SET NAMES utf8mb4;

-- ============================================================
-- TABLA: plantillas_correo
-- ============================================================
CREATE TABLE IF NOT EXISTS plantillas_correo (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug        VARCHAR(50) NOT NULL UNIQUE,
    nombre      VARCHAR(100) NOT NULL,
    asunto      VARCHAR(255) NOT NULL,
    contenido   TEXT NOT NULL,
    variables   TEXT NOT NULL COMMENT 'JSON array of available variable names',
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: correos_log
-- ============================================================
CREATE TABLE IF NOT EXISTS correos_log (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    destinatario  VARCHAR(255) NOT NULL,
    asunto        VARCHAR(255) NOT NULL,
    fecha_envio   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado        ENUM('enviado','error') NOT NULL DEFAULT 'enviado',
    error_mensaje TEXT DEFAULT NULL,
    INDEX idx_correos_fecha (fecha_envio DESC),
    INDEX idx_correos_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA: Plantillas de correo
-- ============================================================

INSERT INTO plantillas_correo (slug, nombre, asunto, contenido, variables) VALUES
('welcome', 'Bienvenida', 'Bienvenido/a a VILUNA Joyería, {nombre_cliente}',
 '<h1>¡Bienvenido/a, {nombre_cliente}!</h1><p>Gracias por registrarte en <strong>VILUNA Joyería</strong>.</p><p>Tu cuenta ha sido creada con el correo: <strong>{correo_cliente}</strong></p><p>Ya puedes iniciar sesión y explorar nuestra colección exclusiva.</p><p><a href="{enlace_login}">Iniciar sesión</a></p><p>Si tienes alguna pregunta, contáctanos a {correo_tienda}.</p><p>¡Bienvenido/a a la familia VILUNA!</p>',
 '["nombre_cliente","correo_cliente","enlace_login","correo_tienda"]'),

('password_reset', 'Recuperación de contraseña', 'Restablece tu contraseña - VILUNA',
 '<h1>Recuperación de contraseña</h1><p>Hola {nombre_cliente},</p><p>Recibimos una solicitud para restablecer tu contraseña.</p><p><a href="{enlace_reset}">Restablecer contraseña</a></p><p>Este enlace expira en {expiracion_minutos} minutos.</p><p>Si no solicitaste este cambio, ignora este correo.</p>',
 '["nombre_cliente","enlace_reset","expiracion_minutos"]'),

('order_confirmation', 'Confirmación de pedido', 'Pedido #{numero_pedido} confirmado - VILUNA',
 '<h1>¡Gracias por tu compra!</h1><p>Hola {nombre_cliente},</p><p>Tu pedido <strong>#{numero_pedido}</strong> ha sido recibido exitosamente.</p><p><strong>Fecha:</strong> {fecha_pedido}</p><p><strong>Productos:</strong></p>{detalle_productos}<p><strong>Dirección de envío:</strong> {direccion_envio}</p><p><strong>Método de pago:</strong> {metodo_pago}</p><p><strong>Total:</strong> Q{total_pedido}</p><p>Te notificaremos cuando haya cambios en el estado de tu pedido.</p>',
 '["nombre_cliente","numero_pedido","fecha_pedido","detalle_productos","direccion_envio","metodo_pago","total_pedido"]'),

('payment_confirmed', 'Pago confirmado', 'Pago confirmado - Pedido #{numero_pedido} - VILUNA',
 '<h1>Pago confirmado</h1><p>Hola {nombre_cliente},</p><p>El pago de tu pedido <strong>#{numero_pedido}</strong> ha sido confirmado.</p><p><strong>Monto pagado:</strong> Q{monto_pagado}</p><p><strong>Fecha de confirmación:</strong> {fecha_confirmacion}</p><p><strong>Estado actual:</strong> {estado_pedido}</p><p>Tu pedido será preparado próximamente.</p>',
 '["nombre_cliente","numero_pedido","monto_pagado","fecha_confirmacion","estado_pedido"]'),

('order_status', 'Cambio de estado de pedido', 'Tu pedido #{numero_pedido} - {nuevo_estado} - VILUNA',
 '<h1>Actualización de tu pedido</h1><p>Hola {nombre_cliente},</p><p>El estado de tu pedido <strong>#{numero_pedido}</strong> ha cambiado.</p><p><strong>Nuevo estado:</strong> {nuevo_estado}</p><p><strong>Fecha:</strong> {fecha_actualizacion}</p><p>{comentarios}</p><p><a href="{enlace_pedido}">Ver mi pedido</a></p>',
 '["nombre_cliente","numero_pedido","nuevo_estado","fecha_actualizacion","comentarios","enlace_pedido"]'),

('admin_new_user', 'Notificación: Nuevo usuario', 'Nuevo registro de usuario - VILUNA',
 '<h1>Nuevo usuario registrado</h1><p>Se ha registrado un nuevo usuario en la tienda.</p><p><strong>Nombre:</strong> {nombre_usuario}</p><p><strong>Correo:</strong> {correo_usuario}</p><p><strong>Fecha:</strong> {fecha_registro}</p>',
 '["nombre_usuario","correo_usuario","fecha_registro"]'),

('admin_new_order', 'Notificación: Nuevo pedido', 'Nuevo pedido #{numero_pedido} - VILUNA',
 '<h1>Nuevo pedido recibido</h1><p>Se ha creado un nuevo pedido en la tienda.</p><p><strong>Pedido #:</strong> {numero_pedido}</p><p><strong>Cliente:</strong> {nombre_cliente}</p><p><strong>Total:</strong> Q{total_pedido}</p><p><strong>Método de pago:</strong> {metodo_pago}</p><p><strong>Fecha:</strong> {fecha_pedido}</p>',
 '["numero_pedido","nombre_cliente","total_pedido","metodo_pago","fecha_pedido"]'),

('admin_payment_received', 'Notificación: Comprobante recibido', 'Comprobante de pago recibido - Pedido #{numero_pedido} - VILUNA',
 '<h1>Comprobante de pago recibido</h1><p>El cliente ha subido un comprobante de pago.</p><p><strong>Pedido #:</strong> {numero_pedido}</p><p><strong>Cliente:</strong> {nombre_cliente}</p><p><strong>Monto:</strong> Q{monto_pedido}</p><p><strong>Fecha:</strong> {fecha_comprobante}</p><p>Revisa el comprobante en el panel de administración.</p>',
 '["numero_pedido","nombre_cliente","monto_pedido","fecha_comprobante"]'),

('admin_new_review', 'Notificación: Nueva reseña', 'Nueva reseña pendiente de aprobación - VILUNA',
 '<h1>Nueva reseña pendiente</h1><p>Un cliente ha dejado una nueva reseña.</p><p><strong>Producto:</strong> {nombre_producto}</p><p><strong>Cliente:</strong> {nombre_cliente}</p><p><strong>Calificación:</strong> {calificacion}/5</p><p><strong>Comentario:</strong> {comentario}</p><p>Revisa y aprueba la reseña desde el panel de administración.</p>',
 '["nombre_producto","nombre_cliente","calificacion","comentario"]');
