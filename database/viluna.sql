-- ============================================================
-- VILUNA Jewelry Store — Script SQL completo
-- MySQL 8+
-- Ejecutar en Laragon: mysql -u root -p < database/viluna.sql
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

DROP DATABASE IF EXISTS viluna;
CREATE DATABASE viluna
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE viluna;

-- ============================================================
-- TABLA: categorias
-- ============================================================
CREATE TABLE categorias (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100)  NOT NULL,
    slug        VARCHAR(120)  NOT NULL UNIQUE,
    descripcion TEXT,
    imagen      VARCHAR(255)  DEFAULT NULL,
    activo      TINYINT(1)    NOT NULL DEFAULT 1,
    CONSTRAINT chk_categorias_nombre CHECK (CHAR_LENGTH(nombre) > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: usuarios
-- ============================================================
CREATE TABLE usuarios (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre               VARCHAR(100)  NOT NULL,
    apellido             VARCHAR(100)  NOT NULL,
    correo               VARCHAR(180)  NOT NULL UNIQUE,
    password             VARCHAR(255)  DEFAULT NULL,          -- NULL para cuentas OAuth
    telefono             VARCHAR(30)   DEFAULT NULL,
    direccion            TEXT          DEFAULT NULL,
    rol                  ENUM('cliente','admin') NOT NULL DEFAULT 'cliente',
    verificado           TINYINT(1)    NOT NULL DEFAULT 0,
    codigo_verificacion  VARCHAR(10)   DEFAULT NULL,
    reset_token          VARCHAR(100)  DEFAULT NULL,
    reset_token_expires  DATETIME      DEFAULT NULL,
    google_id            VARCHAR(100)  DEFAULT NULL,
    fecha_creacion       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: productos
-- ============================================================
CREATE TABLE productos (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id   INT UNSIGNED  NOT NULL,
    nombre         VARCHAR(200)  NOT NULL,
    slug           VARCHAR(220)  NOT NULL UNIQUE,
    descripcion    TEXT          NOT NULL,
    precio         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    descuento      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock          INT UNSIGNED  NOT NULL DEFAULT 0,
    destacado      TINYINT(1)    NOT NULL DEFAULT 0,
    activo         TINYINT(1)    NOT NULL DEFAULT 1,
    fecha_creacion DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_productos_categoria FOREIGN KEY (categoria_id)
        REFERENCES categorias(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_productos_precio    CHECK (precio   >= 0),
    CONSTRAINT chk_productos_descuento CHECK (descuento >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: producto_imagenes
-- ============================================================
CREATE TABLE producto_imagenes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    producto_id INT UNSIGNED  NOT NULL,
    ruta        VARCHAR(255)  NOT NULL,
    es_principal TINYINT(1)   NOT NULL DEFAULT 0,
    orden       TINYINT UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT fk_pimg_producto FOREIGN KEY (producto_id)
        REFERENCES productos(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: cupones
-- ============================================================
CREATE TABLE cupones (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo           VARCHAR(50)   NOT NULL UNIQUE,
    porcentaje       DECIMAL(5,2)  NOT NULL,
    fecha_expiracion DATETIME      NOT NULL,
    limite_usos      INT UNSIGNED  NOT NULL DEFAULT 100,
    usos_actuales    INT UNSIGNED  NOT NULL DEFAULT 0,
    activo           TINYINT(1)    NOT NULL DEFAULT 1,
    CONSTRAINT chk_cupones_porcentaje CHECK (porcentaje > 0 AND porcentaje <= 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: ordenes
-- ============================================================
CREATE TABLE ordenes (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id        INT UNSIGNED  NOT NULL,
    cupon_id          INT UNSIGNED  DEFAULT NULL,
    metodo_pago       ENUM('contra_entrega','transferencia') NOT NULL,
    estado            ENUM('pendiente','pagada','en_preparacion','enviada','entregada','cancelada')
                          NOT NULL DEFAULT 'pendiente',
    subtotal          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    descuento_cupon   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total             DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    direccion_entrega TEXT          NOT NULL,
    comprobante_ruta  VARCHAR(255)  DEFAULT NULL,
    fecha_creacion    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ordenes_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_ordenes_cupon FOREIGN KEY (cupon_id)
        REFERENCES cupones(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: orden_detalle
-- ============================================================
CREATE TABLE orden_detalle (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    orden_id          INT UNSIGNED  NOT NULL,
    producto_id       INT UNSIGNED  NOT NULL,
    cantidad          INT UNSIGNED  NOT NULL DEFAULT 1,
    precio_unitario   DECIMAL(10,2) NOT NULL,
    descuento_unitario DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    CONSTRAINT fk_od_orden    FOREIGN KEY (orden_id)
        REFERENCES ordenes(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_od_producto FOREIGN KEY (producto_id)
        REFERENCES productos(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: wishlist
-- ============================================================
CREATE TABLE wishlist (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id     INT UNSIGNED NOT NULL,
    producto_id    INT UNSIGNED NOT NULL,
    fecha_agregado DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_wishlist (usuario_id, producto_id),
    CONSTRAINT fk_wishlist_usuario  FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_wishlist_producto FOREIGN KEY (producto_id)
        REFERENCES productos(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: newsletter
-- ============================================================
CREATE TABLE newsletter (
    id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    correo             VARCHAR(180) NOT NULL UNIQUE,
    fecha_suscripcion  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: configuracion
-- ============================================================
CREATE TABLE configuracion (
    id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(80)  NOT NULL UNIQUE,
    valor TEXT         NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- ÍNDICES — Rendimiento y búsqueda
-- ============================================================

-- Búsqueda de texto libre en productos
ALTER TABLE productos ADD FULLTEXT INDEX ft_productos (nombre, descripcion);

-- Consultas frecuentes
CREATE INDEX idx_productos_activo_destacado ON productos (activo, destacado);
CREATE INDEX idx_productos_categoria        ON productos (categoria_id);
CREATE INDEX idx_productos_fecha            ON productos (fecha_creacion DESC);
CREATE INDEX idx_ordenes_usuario            ON ordenes (usuario_id);
CREATE INDEX idx_ordenes_estado             ON ordenes (estado);
CREATE INDEX idx_wishlist_usuario           ON wishlist (usuario_id);
CREATE INDEX idx_pimg_producto_orden        ON producto_imagenes (producto_id, orden);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- DATOS DE CONFIGURACIÓN
-- ============================================================
INSERT INTO configuracion (clave, valor) VALUES
('nombre_tienda',      'VILUNA Joyería'),
('correo_contacto',    'contacto@viluna.com'),
('whatsapp',           '50212345678'),
('direccion',          'Ciudad de Guatemala, Guatemala'),
('facebook',           'https://facebook.com/vilunajoyeria'),
('instagram',          'https://instagram.com/vilunajoyeria'),
('banco_nombre',       'Banco Industrial'),
('banco_cuenta',       '123-456789-0'),
('banco_tipo',         'Monetaria'),
('banco_beneficiario', 'VILUNA Joyería S.A.'),
('metadescripcion',    'VILUNA — Joyería fina y exclusiva. Descubre nuestra colección de anillos, collares, pulseras y más.'),
('slogan',             'Elegancia que perdura'),
('whatsapp_mensaje',   'Hola, me interesa conocer más sobre sus joyas.');

-- ============================================================
-- USUARIO ADMINISTRADOR POR DEFECTO
-- Correo:    admin@viluna.com
-- Contraseña: Admin123*   (hash bcrypt generado con PHP)
-- php -r "echo password_hash('Admin123*', PASSWORD_BCRYPT);"
-- ============================================================
INSERT INTO usuarios (nombre, apellido, correo, password, rol, verificado) VALUES
('Admin', 'VILUNA', 'admin@viluna.com',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'admin', 1);
-- NOTA: El hash anterior es solo un placeholder válido de bcrypt.
-- En producción regenerar con: password_hash('Admin123*', PASSWORD_BCRYPT)

-- Cliente de prueba: cliente@viluna.com / Test123*
INSERT INTO usuarios (nombre, apellido, correo, password, rol, verificado) VALUES
('María', 'González', 'cliente@viluna.com',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'cliente', 1);

-- ============================================================
-- CATEGORÍAS
-- ============================================================
INSERT INTO categorias (nombre, slug, descripcion, activo) VALUES
('Anillos',   'anillos',   'Anillos de oro, plata y piedras preciosas',  1),
('Pulseras',  'pulseras',  'Pulseras elegantes para toda ocasión',        1),
('Collares',  'collares',  'Collares y cadenas de alta joyería',          1),
('Aretes',    'aretes',    'Aretes y pendientes exclusivos',               1),
('Dijes',     'dijes',     'Dijes y charms de colección',                 1),
('Relojes',   'relojes',   'Relojes de lujo para dama y caballero',       1),
('Otros',     'otros',     'Accesorios y joyería variada',                1);

-- ============================================================
-- PRODUCTOS DE PRUEBA
-- ============================================================
INSERT INTO productos (categoria_id, nombre, slug, descripcion, precio, descuento, stock, destacado, activo) VALUES
(1, 'Anillo Solitario Oro 18k',    'anillo-solitario-oro-18k',
 'Elegante anillo solitario en oro amarillo 18 quilates con diamante central de 0.25 ct.',
 2500.00, 0.00, 5, 1, 1),

(1, 'Anillo Compromiso Platino',   'anillo-compromiso-platino',
 'Anillo de compromiso en platino 950 con diamante brillante de 0.50 ct. Diseño clásico y atemporal.',
 5800.00, 500.00, 3, 1, 1),

(2, 'Pulsera Tennis Oro Blanco',   'pulsera-tennis-oro-blanco',
 'Pulsera tennis en oro blanco 14k con zircones brillantes. 18 cm de longitud.',
 1800.00, 200.00, 8, 1, 1),

(3, 'Collar Cadena Veneciana Oro', 'collar-cadena-veneciana-oro',
 'Cadena veneciana en oro amarillo 18k de 45 cm. Cierre de mosquetón.',
 950.00, 0.00, 12, 0, 1),

(3, 'Collar Corazón con Diamante', 'collar-corazon-con-diamante',
 'Dije corazón en oro blanco 18k con diamante de 0.10 ct. Cadena incluida de 40 cm.',
 1350.00, 150.00, 7, 1, 1),

(4, 'Aretes Perla Natural Gota',   'aretes-perla-natural-gota',
 'Aretes de perla natural forma gota con engaste en plata 925 bañada en oro.',
 450.00, 50.00, 15, 0, 1),

(5, 'Dije Cruz Oro Sólido 14k',    'dije-cruz-oro-solido-14k',
 'Dije cruz en oro sólido 14 quilates. Acabado pulido. Incluye cadena de 45 cm.',
 380.00, 0.00, 20, 0, 1),

(6, 'Reloj Dama Brazalete Dorado', 'reloj-dama-brazalete-dorado',
 'Reloj de dama con brazalete de acero inoxidable bañado en oro. Movimiento cuarzo japonés. Resistente al agua 30m.',
 3200.00, 300.00, 4, 1, 1),

(2, 'Pulsera Identificación Plata','pulsera-identificacion-plata',
 'Pulsera de identificación en plata 925 grabada a mano. Personalizable con nombre.',
 320.00, 0.00, 25, 0, 1),

(4, 'Aretes Aro Oro 18k Pequeños', 'aretes-aro-oro-18k-pequenos',
 'Aretes aro pequeños en oro amarillo 18k de 10 mm de diámetro. Perfectos para uso diario.',
 680.00, 0.00, 18, 0, 1);

-- ============================================================
-- IMÁGENES DE PRODUCTOS (rutas placeholder — reemplazar con imágenes reales)
-- ============================================================
INSERT INTO producto_imagenes (producto_id, ruta, es_principal, orden) VALUES
(1, 'assets/images/productos/placeholder-anillo-1.jpg', 1, 1),
(2, 'assets/images/productos/placeholder-anillo-2.jpg', 1, 1),
(3, 'assets/images/productos/placeholder-pulsera-1.jpg', 1, 1),
(4, 'assets/images/productos/placeholder-collar-1.jpg', 1, 1),
(5, 'assets/images/productos/placeholder-collar-2.jpg', 1, 1),
(6, 'assets/images/productos/placeholder-aretes-1.jpg', 1, 1),
(7, 'assets/images/productos/placeholder-dije-1.jpg', 1, 1),
(8, 'assets/images/productos/placeholder-reloj-1.jpg', 1, 1),
(9, 'assets/images/productos/placeholder-pulsera-2.jpg', 1, 1),
(10,'assets/images/productos/placeholder-aretes-2.jpg', 1, 1);

-- ============================================================
-- CUPÓN DE PRUEBA
-- ============================================================
INSERT INTO cupones (codigo, porcentaje, fecha_expiracion, limite_usos, usos_actuales, activo) VALUES
('VILUNA10', 10.00, DATE_ADD(NOW(), INTERVAL 1 YEAR), 500, 0, 1),
('BIENVENIDO', 15.00, DATE_ADD(NOW(), INTERVAL 6 MONTH), 100, 0, 1);

-- ============================================================
-- ÓRDENES DE PRUEBA
-- ============================================================
INSERT INTO ordenes (usuario_id, metodo_pago, estado, subtotal, descuento_cupon, total, direccion_entrega) VALUES
(2, 'contra_entrega', 'entregada', 2500.00, 0.00, 2500.00, 'Zona 10, Ciudad de Guatemala'),
(2, 'transferencia',  'pagada',    1800.00, 0.00, 1800.00, 'Zona 10, Ciudad de Guatemala');

INSERT INTO orden_detalle (orden_id, producto_id, cantidad, precio_unitario, descuento_unitario) VALUES
(1, 1, 1, 2500.00, 0.00),
(2, 3, 1, 1800.00, 200.00);
