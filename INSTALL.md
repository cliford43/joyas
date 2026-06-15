# VILUNA Jewelry Store — Manual de Instalación

## Requisitos

- PHP 8.1+
- MySQL 8.0+
- Composer 2.x
- Apache con mod_rewrite habilitado
- Laragon (Windows) — recomendado para desarrollo local

---

## Instalación en Laragon (Desarrollo Local)

### 1. Clonar o copiar el proyecto

Copia la carpeta `viluna` dentro de `C:\laragon\www\`:

```
C:\laragon\www\viluna\
```

### 2. Instalar dependencias PHP

Abre la terminal de Laragon (o CMD) y ejecuta:

```bash
cd C:\laragon\www\viluna
composer install
```

### 3. Crear la base de datos

En HeidiSQL, phpMyAdmin o la terminal de MySQL:

```sql
mysql -u root -p < database/viluna.sql
```

O desde phpMyAdmin: importa el archivo `database/viluna.sql`.

### 4. Configurar el entorno

Copia el archivo de configuración:

```bash
copy config\config.example.php config\config.php
```

Edita `config/config.php` con tus datos:

```php
define('APP_URL',  'http://viluna.test');
define('DB_NAME',  'viluna');
define('DB_USER',  'root');
define('DB_PASS',  '');
```

### 5. Configurar virtual host en Laragon

En Laragon: clic derecho → **Apache → sites-enabled → agregar** o usa el menú **Hosts**.

Crear `C:\laragon\etc\apache2\sites-enabled\viluna.conf`:

```apache
<VirtualHost *:80>
    ServerName viluna.test
    DocumentRoot "C:/laragon/www/viluna/public"
    <Directory "C:/laragon/www/viluna/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Reinicia Apache en Laragon.

### 6. Verificar la instalación

Abre el navegador en: `http://viluna.test`

**Credenciales de administrador por defecto:**
- Correo: `admin@viluna.com`
- Contraseña: `Admin123*`

---

## Instalación en GoDaddy Linux (Producción)

### 1. Subir archivos

Sube todos los archivos al directorio raíz de tu hosting (ej: `/public_html/`):

```
/public_html/
  ├── app/
  ├── config/
  ├── core/
  ├── database/
  ├── public/        ← el contenido de esta carpeta debe estar en /public_html/
  ├── routes/
  ├── services/
  ├── storage/
  ├── uploads/
  └── vendor/
```

**Opción recomendada:** Configurar el `DocumentRoot` apuntando a `/public_html/public/` si tienes acceso a cPanel → Apache Configuration.

**Opción alternativa (sin acceso a DocumentRoot):** Copia el contenido de `public/` al directorio raíz (`/public_html/`) y ajusta las rutas en `public/index.php`.

### 2. Crear la base de datos en cPanel

1. Ve a **cPanel → MySQL Databases**.
2. Crea una base de datos: `viluna_db`.
3. Crea un usuario y asígnale todos los privilegios.
4. Importa `database/viluna.sql` desde **phpMyAdmin**.

### 3. Configurar `config/config.php`

```php
define('APP_ENV',  'production');
define('APP_URL',  'https://tudominio.com');
define('DB_HOST',  'localhost');
define('DB_NAME',  'cpanelusr_viluna');
define('DB_USER',  'cpanelusr_viluna');
define('DB_PASS',  'TU_PASSWORD_DB');
```

### 4. Configurar SMTP

GoDaddy bloquea el puerto 25. Usa SMTP autenticado:

```php
define('MAIL_HOST',       'smtpout.secureserver.net');
define('MAIL_PORT',       465);
define('MAIL_ENCRYPTION', 'ssl');
define('MAIL_USERNAME',   'correo@tudominio.com');
define('MAIL_PASSWORD',   'TU_PASSWORD_CORREO');
```

### 5. Permisos de carpetas

```bash
chmod 755 uploads/
chmod 755 uploads/productos/
chmod 755 uploads/comprobantes/
chmod 755 storage/logs/
```

### 6. Verificar mod_rewrite

Asegúrate de que el `.htaccess` en `public/` esté funcionando. Si no, activa `AllowOverride All` en la configuración de Apache del cPanel.

---

## Probar en Laragon — Checklist por Fase

### Fase 1 — Base de Datos
- [ ] `http://viluna.test` carga sin errores de DB
- [ ] phpMyAdmin muestra las tablas creadas

### Fase 2 — Arquitectura MVC
- [ ] `http://viluna.test/ruta-inexistente` muestra página 404

### Fase 4 — Autenticación
- [ ] Registrar nuevo usuario y recibir correo de verificación
- [ ] Login con `admin@viluna.com` / `Admin123*` redirige a `/mi-cuenta`

### Fase 5 — Catálogo
- [ ] `http://viluna.test` muestra productos destacados y categorías
- [ ] El buscador filtra en tiempo real sin recargar

### Fase 6 — Carrito
- [ ] Agregar producto al carrito actualiza el contador del navbar
- [ ] Aplicar cupón `VILUNA10` aplica 10% de descuento

### Fase 7 — Checkout
- [ ] Completar orden contra entrega crea registro en DB
- [ ] Completar orden con transferencia acepta comprobante JPG/PNG/PDF

### Fase 8 — Dashboard Cliente
- [ ] `/mi-cuenta` muestra historial de órdenes
- [ ] Agregar a wishlist actualiza el ícono de corazón

### Fase 9 — Dashboard Admin
- [ ] `/admin` muestra estadísticas y gráfica de ventas
- [ ] CRUD de productos con subida de imágenes funciona
- [ ] Panel de pagos muestra comprobantes pendientes

### Fase 10 — SEO
- [ ] `http://viluna.test/sitemap.xml` retorna XML válido
- [ ] `http://viluna.test/robots.txt` retorna texto correcto
