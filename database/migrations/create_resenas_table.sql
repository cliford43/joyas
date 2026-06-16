-- ============================================================
-- VILUNA — Migración: Tabla resenas (reseñas de productos)
-- ============================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS resenas (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id     INT UNSIGNED NOT NULL,
    producto_id    INT UNSIGNED NOT NULL,
    calificacion   TINYINT UNSIGNED NOT NULL,
    comentario     TEXT NOT NULL,
    estado         ENUM('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
    ip_address     VARCHAR(45) DEFAULT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_usuario_producto (usuario_id, producto_id),
    CONSTRAINT fk_resenas_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_resenas_producto FOREIGN KEY (producto_id)
        REFERENCES productos(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_calificacion CHECK (calificacion >= 1 AND calificacion <= 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_resenas_producto_estado ON resenas (producto_id, estado);
CREATE INDEX idx_resenas_usuario ON resenas (usuario_id);
CREATE INDEX idx_resenas_fecha ON resenas (fecha_creacion DESC);
