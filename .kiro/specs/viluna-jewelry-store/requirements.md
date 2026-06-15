# Documento de Requisitos

## Introducción

VILUNA es una tienda virtual de joyería fina con identidad visual premium. La plataforma permite a los clientes explorar, buscar y comprar joyas en línea, y a los administradores gestionar el catálogo, pedidos, usuarios y pagos. El sistema se desarrollará en PHP 8+ con arquitectura MVC, MySQL 8+, Bootstrap 5 y JavaScript/AJAX, compatible con Laragon (desarrollo local) y hosting Linux GoDaddy (producción).

## Glosario

- **Sistema**: La aplicación web VILUNA en su conjunto.
- **Cliente**: Usuario registrado con rol de comprador.
- **Administrador**: Usuario con rol privilegiado que gestiona la tienda.
- **Producto**: Joya disponible en el catálogo con precio, stock, categoría e imágenes.
- **Categoría**: Agrupación de productos (Anillos, Pulseras, Collares, Aretes, Dijes, Relojes, Otros).
- **Orden**: Registro de una compra realizada por un Cliente.
- **Carrito**: Colección temporal de productos seleccionados por un Cliente antes de finalizar la compra.
- **Comprobante**: Archivo JPG/PNG/PDF subido por el Cliente como evidencia de transferencia bancaria.
- **Dashboard_Cliente**: Panel de control accesible por el Cliente autenticado.
- **Dashboard_Admin**: Panel de control accesible por el Administrador autenticado.
- **Autenticador**: Componente responsable de verificar identidad y gestionar sesiones.
- **Mailer**: Componente que envía correos electrónicos usando PHPMailer.
- **Router**: Componente que mapea URLs a controladores y acciones.
- **DB**: Capa de acceso a base de datos usando PDO con MySQL 8+.
- **Buscador**: Componente de búsqueda y filtrado de productos vía AJAX.
- **Panel_Pagos**: Componente del Dashboard_Admin para gestión de pagos por transferencia.
- **Wishlist**: Lista de productos guardados por el Cliente para consulta futura.
- **Cupon**: Código de descuento aplicable al total de una Orden.

---

## Requisitos

### Requisito 1: Base de Datos

**Historia de usuario:** Como desarrollador, quiero un esquema de base de datos normalizado, para que los datos de la tienda se almacenen de forma íntegra y consultable.

#### Criterios de Aceptación

1. THE DB SHALL crear las tablas: `usuarios`, `categorias`, `productos`, `producto_imagenes`, `ordenes`, `orden_detalle`, `configuracion`, `wishlist`, `cupones`, `newsletter`.
2. THE DB SHALL definir la tabla `usuarios` con los campos: `id`, `nombre`, `apellido`, `correo` (único), `password`, `telefono`, `direccion`, `rol` (enum: cliente/admin), `verificado` (boolean), `codigo_verificacion`, `fecha_creacion`.
3. THE DB SHALL definir la tabla `productos` con los campos: `id`, `categoria_id` (FK), `nombre`, `descripcion`, `precio` (decimal), `descuento` (decimal), `stock` (int), `destacado` (boolean), `fecha_creacion`, `activo` (boolean).
4. THE DB SHALL definir claves foráneas con restricciones de integridad referencial entre `productos.categoria_id → categorias.id`, `producto_imagenes.producto_id → productos.id`, `ordenes.usuario_id → usuarios.id`, `orden_detalle.orden_id → ordenes.id`, `orden_detalle.producto_id → productos.id`.
5. THE DB SHALL incluir un script SQL ejecutable que crea toda la estructura y datos de prueba, incluyendo un usuario administrador con correo `admin@viluna.com` y contraseña `Admin123*` almacenada con `password_hash()`.

---

### Requisito 2: Arquitectura MVC

**Historia de usuario:** Como desarrollador, quiero una arquitectura MVC clara y desacoplada, para que el código sea mantenible y extensible.

#### Criterios de Aceptación

1. THE Sistema SHALL organizar el código en las carpetas: `/app/controllers`, `/app/models`, `/app/views`, `/public/assets`, `/uploads`, `/config`, `/routes`, `/storage`, `/vendor`.
2. THE Router SHALL mapear URLs amigables a controladores y acciones sin exponer rutas de archivo del sistema.
3. THE Sistema SHALL cargar dependencias mediante Composer (autoload PSR-4).
4. WHEN una ruta no existe, THE Router SHALL redirigir al Cliente a una página 404 personalizada.
5. THE Sistema SHALL separar la configuración de entorno (base de datos, correo, OAuth) en archivos `.env` o `config.php` excluidos del repositorio.

---

### Requisito 3: Identidad Visual y Diseño

**Historia de usuario:** Como cliente, quiero una interfaz elegante y premium, para que la experiencia de compra refleje la calidad de las joyas.

#### Criterios de Aceptación

1. THE Sistema SHALL aplicar la paleta de colores: Dorado (`#D4AF37`), Negro (`#111111`) y Blanco (`#FFFFFF`) de forma consistente en todos los componentes visuales.
2. THE Sistema SHALL entregar el logo VILUNA en los formatos: SVG escalable, PNG con fondo transparente, versión horizontal, versión vertical y favicon `.ico`/`.png` de 32×32 px.
3. THE Sistema SHALL ser completamente responsive usando Bootstrap 5, adaptándose a resoluciones de escritorio (≥1200px), tablet (768px–1199px) y móvil (<768px).
4. WHEN el Sistema carga cualquier página, THE Sistema SHALL mostrar el favicon VILUNA en la pestaña del navegador.

---

### Requisito 4: Página Principal

**Historia de usuario:** Como cliente, quiero una página de inicio atractiva, para que pueda descubrir productos y categorías rápidamente.

#### Criterios de Aceptación

1. THE Sistema SHALL mostrar un banner hero con imagen promocional, mensaje de marca y botón de llamada a la acción que dirija al catálogo.
2. THE Sistema SHALL mostrar las categorías activas (Anillos, Pulseras, Collares, Aretes, Dijes, Relojes, Otros) con imagen representativa y enlace a su listado.
3. THE Sistema SHALL mostrar una sección de "Productos más vendidos" calculados a partir del historial de `orden_detalle`.
4. THE Sistema SHALL mostrar una sección de "Productos nuevos" con los productos creados en los últimos 7 días naturales.
5. THE Sistema SHALL mostrar una sección de "Productos destacados" con los productos marcados como `destacado = true` desde el Dashboard_Admin.
6. WHEN el administrador activa o desactiva el campo `destacado` de un Producto, THE Sistema SHALL reflejar el cambio en la página principal en la siguiente carga.

---

### Requisito 5: Buscador Avanzado

**Historia de usuario:** Como cliente, quiero buscar y filtrar joyas, para que pueda encontrar exactamente lo que busco sin navegar todo el catálogo.

#### Criterios de Aceptación

1. THE Buscador SHALL aceptar como parámetros de filtrado: texto libre, precio mínimo, precio máximo, categoría y criterio de ordenamiento.
2. WHEN el Cliente modifica cualquier filtro, THE Buscador SHALL enviar una petición AJAX y actualizar los resultados sin recargar la página, en un tiempo máximo de 1 segundo bajo condiciones normales de red local.
3. THE Buscador SHALL permitir ordenar resultados por: más recientes, más antiguos, precio ascendente, precio descendente y con descuento.
4. WHEN la búsqueda no produce resultados, THE Buscador SHALL mostrar un mensaje informativo al Cliente indicando que no se encontraron productos.
5. IF el Cliente ingresa un precio mínimo mayor al precio máximo, THEN THE Buscador SHALL mostrar un mensaje de validación y no ejecutar la búsqueda.

---

### Requisito 6: Detalle de Producto

**Historia de usuario:** Como cliente, quiero ver toda la información de una joya, para que pueda tomar una decisión de compra informada.

#### Criterios de Aceptación

1. THE Sistema SHALL mostrar en la página de detalle: nombre, descripción, precio original, precio con descuento (si aplica), stock disponible y categoría del Producto.
2. THE Sistema SHALL mostrar una galería de imágenes del Producto con funcionalidad de zoom al hacer clic o hover sobre la imagen principal.
3. WHEN el stock del Producto es 0, THE Sistema SHALL deshabilitar el botón "Agregar al carrito" y mostrar la etiqueta "Sin stock".
4. THE Sistema SHALL mostrar una sección de "Productos relacionados" con hasta 4 productos de la misma categoría del Producto visualizado.
5. THE Sistema SHALL incluir meta tags dinámicos (`title`, `description`, `og:image`) con los datos del Producto para SEO.

---

### Requisito 7: Carrito de Compras

**Historia de usuario:** Como cliente, quiero gestionar mi carrito antes de pagar, para que pueda revisar y ajustar mi pedido.

#### Criterios de Aceptación

1. WHEN el Cliente hace clic en "Agregar al carrito", THE Carrito SHALL añadir el Producto con cantidad 1, o incrementar la cantidad si el Producto ya estaba en el Carrito.
2. THE Carrito SHALL permitir al Cliente modificar la cantidad de cada ítem con valores enteros positivos entre 1 y el stock disponible del Producto.
3. THE Carrito SHALL permitir al Cliente eliminar ítems individuales o vaciar el Carrito completo.
4. THE Carrito SHALL calcular y mostrar el subtotal por ítem, el descuento aplicado y el total general de la Orden en tiempo real.
5. IF el Cliente intenta agregar una cantidad que supera el stock disponible, THEN THE Carrito SHALL mostrar un mensaje de error y limitar la cantidad al stock máximo disponible.
6. WHERE el Cliente tiene un Cupon válido activo, THE Carrito SHALL aplicar el porcentaje de descuento del Cupon al total antes de mostrar el monto final.

---

### Requisito 8: Checkout y Métodos de Pago

**Historia de usuario:** Como cliente, quiero finalizar mi compra eligiendo un método de pago, para que pueda recibir mis joyas en casa.

#### Criterios de Aceptación

1. THE Sistema SHALL ofrecer dos métodos de pago en el checkout: "Contra entrega" y "Transferencia bancaria".
2. WHERE el Cliente selecciona "Transferencia bancaria", THE Sistema SHALL mostrar los datos bancarios de VILUNA y un formulario para subir el Comprobante.
3. WHEN el Cliente sube un Comprobante, THE Sistema SHALL aceptar únicamente archivos con extensión JPG, PNG o PDF con tamaño máximo de 5 MB.
4. IF el Cliente sube un archivo que no cumple el tipo o tamaño permitido, THEN THE Sistema SHALL mostrar un mensaje de error descriptivo y rechazar el archivo.
5. WHEN el Cliente confirma la Orden, THE Sistema SHALL crear el registro en `ordenes` con estado `pendiente`, reducir el stock de cada Producto en `orden_detalle` y enviar un correo de confirmación al Cliente mediante el Mailer.
6. WHEN el Cliente confirma la Orden, THE Sistema SHALL mostrar una página de confirmación con el número de Orden y el resumen de compra.

---

### Requisito 9: Registro y Verificación de Usuarios

**Historia de usuario:** Como visitante, quiero crear una cuenta, para que pueda comprar y rastrear mis pedidos.

#### Criterios de Aceptación

1. THE Sistema SHALL ofrecer registro tradicional con los campos: nombre, apellido, correo, contraseña y confirmación de contraseña.
2. THE Sistema SHALL ofrecer registro mediante Google OAuth como alternativa al registro tradicional.
3. WHEN un visitante completa el registro tradicional, THE Autenticador SHALL generar un código de verificación de 6 dígitos, almacenarlo en `usuarios.codigo_verificacion` y enviar un correo de activación mediante el Mailer.
4. WHEN el visitante ingresa el código de verificación correcto, THE Autenticador SHALL marcar `usuarios.verificado = true` y activar la cuenta.
5. IF el visitante intenta iniciar sesión con una cuenta no verificada, THEN THE Autenticador SHALL mostrar un mensaje indicando que debe verificar su correo y ofrecer reenviar el código.
6. THE Autenticador SHALL almacenar contraseñas usando `password_hash()` con el algoritmo `PASSWORD_BCRYPT` y nunca almacenar contraseñas en texto plano.

---

### Requisito 10: Inicio de Sesión y Recuperación de Contraseña

**Historia de usuario:** Como cliente registrado, quiero iniciar sesión de forma segura, para que pueda acceder a mi cuenta y mis pedidos.

#### Criterios de Aceptación

1. THE Autenticador SHALL permitir inicio de sesión con correo y contraseña usando `password_verify()`.
2. THE Autenticador SHALL permitir inicio de sesión con Google OAuth, creando la cuenta automáticamente si el correo no existe.
3. WHEN el Cliente solicita recuperación de contraseña, THE Mailer SHALL enviar un enlace de restablecimiento con token único de validez máxima de 1 hora.
4. WHEN el Cliente accede al enlace de restablecimiento con token válido, THE Autenticador SHALL permitir ingresar y confirmar una nueva contraseña.
5. IF el Cliente accede al enlace de restablecimiento con token expirado o inválido, THEN THE Autenticador SHALL mostrar un mensaje de error y redirigir al formulario de solicitud de recuperación.
6. THE Autenticador SHALL invalidar el token de restablecimiento tras su primer uso exitoso.

---

### Requisito 11: Dashboard del Cliente

**Historia de usuario:** Como cliente autenticado, quiero un panel personal, para que pueda gestionar mi información y revisar mis pedidos.

#### Criterios de Aceptación

1. WHILE el Cliente está autenticado, THE Dashboard_Cliente SHALL mostrar un menú lateral con las secciones: Mi Perfil, Mis Órdenes y Mi Wishlist.
2. THE Dashboard_Cliente SHALL permitir al Cliente editar nombre, apellido, teléfono y dirección, y guardar los cambios en la base de datos.
3. THE Dashboard_Cliente SHALL permitir al Cliente cambiar su contraseña, requiriendo la contraseña actual como verificación previa.
4. THE Dashboard_Cliente SHALL listar el historial de Órdenes del Cliente con: número de orden, fecha, estado, método de pago y total.
5. WHEN el Cliente hace clic en una Orden, THE Dashboard_Cliente SHALL mostrar el detalle completo con productos, cantidades, precios unitarios y estado actual.
6. IF un visitante no autenticado intenta acceder al Dashboard_Cliente, THEN THE Autenticador SHALL redirigirlo al formulario de inicio de sesión.

---

### Requisito 12: Dashboard del Administrador

**Historia de usuario:** Como administrador, quiero un panel de control completo, para que pueda gestionar todos los aspectos de la tienda.

#### Criterios de Aceptación

1. IF un usuario sin rol `admin` intenta acceder al Dashboard_Admin, THEN THE Autenticador SHALL devolver un error 403 y redirigir al inicio.
2. THE Dashboard_Admin SHALL mostrar estadísticas en tiempo real: total de ventas (suma de `ordenes.total` con estado `entregada`), número de usuarios registrados, número de productos activos y productos más vendidos.
3. THE Dashboard_Admin SHALL mostrar una gráfica de ventas por mes de los últimos 12 meses usando datos de `ordenes`.
4. THE Dashboard_Admin SHALL permitir gestión CRUD completa de Categorías: crear, leer, actualizar y cambiar estado activo/inactivo.
5. THE Dashboard_Admin SHALL permitir gestión CRUD completa de Productos con: nombre, descripción, precio, descuento, stock, categoría, estado activo/inactivo, destacado y hasta 10 imágenes por producto.
6. WHEN el Administrador sube imágenes de un Producto, THE Sistema SHALL aceptar archivos JPG y PNG con tamaño máximo de 2 MB por imagen y almacenarlos en `/uploads/productos/`.
7. THE Dashboard_Admin SHALL permitir gestión de Órdenes filtradas por estado: pendiente, pagada, en preparación, enviada, entregada y cancelada.
8. WHEN el Administrador cambia el estado de una Orden, THE Mailer SHALL enviar una notificación al Cliente con el nuevo estado.
9. THE Panel_Pagos SHALL listar los Comprobantes pendientes de revisión con opción de aprobar o rechazar, cambiando el estado de la Orden correspondientemente.
10. WHEN el Panel_Pagos aprueba un Comprobante, THE Sistema SHALL cambiar el estado de la Orden a `pagada` y notificar al Cliente mediante el Mailer.
11. THE Dashboard_Admin SHALL permitir gestión de Usuarios: listar, ver detalle, activar y desactivar cuentas.

---

### Requisito 13: Seguridad

**Historia de usuario:** Como dueño de la tienda, quiero que la plataforma sea segura, para que los datos de los clientes y las transacciones estén protegidos.

#### Criterios de Aceptación

1. THE Sistema SHALL proteger todos los formularios con tokens CSRF únicos por sesión, validados en el servidor antes de procesar cualquier petición POST.
2. THE Sistema SHALL sanitizar todas las salidas HTML usando `htmlspecialchars()` para prevenir ataques XSS.
3. THE DB SHALL usar PDO con consultas preparadas y parámetros vinculados para todas las interacciones con la base de datos, previniendo inyección SQL.
4. THE Autenticador SHALL destruir la sesión completa al cerrar sesión y regenerar el ID de sesión tras autenticación exitosa.
5. THE Sistema SHALL validar el tipo MIME real de archivos subidos en el servidor, independientemente de la extensión declarada por el Cliente.
6. THE Sistema SHALL incluir cabeceras de seguridad HTTP: `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY` y `Content-Security-Policy` básica.

---

### Requisito 14: SEO y Rendimiento

**Historia de usuario:** Como dueño de la tienda, quiero que VILUNA sea indexable y rápida, para que los clientes puedan encontrarla en buscadores.

#### Criterios de Aceptación

1. THE Router SHALL generar URLs amigables en formato `/categoria/nombre-producto` sin parámetros query visibles para productos y categorías.
2. THE Sistema SHALL generar meta tags dinámicos `<title>` y `<meta name="description">` específicos para cada página de producto y categoría.
3. THE Sistema SHALL servir un archivo `sitemap.xml` generado dinámicamente con las URLs de productos y categorías activos.
4. THE Sistema SHALL servir un archivo `robots.txt` que permita indexación de páginas públicas y bloquee rutas de administración.

---

### Requisito 15: Correo Electrónico Transaccional

**Historia de usuario:** Como cliente, quiero recibir correos en eventos clave, para que pueda estar informado sobre mi cuenta y pedidos.

#### Criterios de Aceptación

1. THE Mailer SHALL enviar un correo de bienvenida con código de verificación de 6 dígitos al correo del Cliente tras el registro tradicional.
2. WHEN una Orden es confirmada, THE Mailer SHALL enviar al Cliente un correo con el resumen de la Orden en un plazo máximo de 30 segundos.
3. WHEN el Administrador cambia el estado de una Orden, THE Mailer SHALL enviar una notificación al Cliente con el nuevo estado y número de Orden.
4. WHEN el Cliente solicita recuperación de contraseña, THE Mailer SHALL enviar el enlace de restablecimiento en un plazo máximo de 30 segundos.
5. IF el Mailer falla al enviar un correo, THEN THE Sistema SHALL registrar el error en `/storage/logs/mail.log` y no interrumpir el flujo de la operación principal.

---

### Requisito 16: Wishlist

**Historia de usuario:** Como cliente, quiero guardar productos en una lista de deseos, para que pueda revisarlos y comprarlos más adelante.

#### Criterios de Aceptación

1. WHILE el Cliente está autenticado, THE Wishlist SHALL permitir agregar o quitar un Producto con un solo clic desde la página de listado o detalle.
2. THE Dashboard_Cliente SHALL mostrar todos los Productos guardados en la Wishlist con opción de agregarlos directamente al Carrito.
3. WHEN el Cliente agrega un Producto ya presente en su Wishlist, THE Wishlist SHALL quitar el Producto (comportamiento toggle).

---

### Requisito 17: Cupones de Descuento

**Historia de usuario:** Como administrador, quiero crear cupones de descuento, para que pueda ofrecer promociones a los clientes.

#### Criterios de Aceptación

1. THE Dashboard_Admin SHALL permitir crear Cupones con: código único, porcentaje de descuento, fecha de expiración y límite de usos.
2. WHEN el Cliente ingresa un código de Cupon en el Carrito, THE Sistema SHALL validar que el Cupon existe, está activo, no ha expirado y no superó su límite de usos.
3. IF el Cupon no supera la validación, THEN THE Sistema SHALL mostrar un mensaje descriptivo indicando el motivo de rechazo.
4. WHEN una Orden se confirma con un Cupon aplicado, THE Sistema SHALL incrementar el contador de usos del Cupon en 1.

---

### Requisito 18: Newsletter

**Historia de usuario:** Como dueño de la tienda, quiero capturar correos para newsletter, para que pueda comunicar promociones a suscriptores.

#### Criterios de Aceptación

1. THE Sistema SHALL mostrar un formulario de suscripción al newsletter en el footer de todas las páginas públicas.
2. WHEN un visitante ingresa su correo y confirma la suscripción, THE Sistema SHALL almacenar el correo en la tabla `newsletter` con fecha de suscripción.
3. IF el correo ya existe en la tabla `newsletter`, THEN THE Sistema SHALL mostrar un mensaje indicando que ya está suscrito sin crear un duplicado.

---

### Requisito 19: Integración con WhatsApp

**Historia de usuario:** Como cliente, quiero contactar a la tienda por WhatsApp, para que pueda resolver dudas antes de comprar.

#### Criterios de Aceptación

1. THE Sistema SHALL mostrar un botón flotante de WhatsApp en todas las páginas públicas con el número configurado en la tabla `configuracion`.
2. WHEN el Cliente hace clic en el botón de WhatsApp, THE Sistema SHALL abrir un enlace `https://wa.me/{numero}` con un mensaje predeterminado en una nueva pestaña.

---

### Requisito 20: Panel de Configuración General

**Historia de usuario:** Como administrador, quiero configurar parámetros generales de la tienda, para que pueda actualizar datos sin modificar código.

#### Criterios de Aceptación

1. THE Dashboard_Admin SHALL mostrar un panel de configuración general con campos editables para: nombre de la tienda, correo de contacto, teléfono WhatsApp, dirección física, redes sociales y datos bancarios para transferencias.
2. WHEN el Administrador guarda la configuración, THE Sistema SHALL actualizar los registros correspondientes en la tabla `configuracion` y reflejar los cambios en el frontend en la siguiente carga de página.
3. THE Sistema SHALL cargar los valores de configuración desde la tabla `configuracion` al inicializar cada petición, usando caché en sesión para evitar múltiples consultas por petición.
