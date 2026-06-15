<?php

use Core\Router;

/** @var Router $router */

// ─── Páginas Públicas ─────────────────────────────────────────────────────────
$router->get('/', 'HomeController@index');

// ─── Catálogo y Búsqueda ──────────────────────────────────────────────────────
$router->get('/catalogo', 'CatalogController@index');
$router->get('/catalogo/{categoria}', 'CatalogController@category');
$router->get('/producto/{slug}', 'ProductController@show');
$router->get('/buscar', 'SearchController@handle');

// ─── SEO ──────────────────────────────────────────────────────────────────────
$router->get('/sitemap.xml', 'SeoController@sitemap');
$router->get('/robots.txt', 'SeoController@robots');

// ─── Archivos subidos ─────────────────────────────────────────────────────────
$router->get('/uploads/branding/{filename}', 'UploadController@brandingAsset');
$router->get('/uploads/categorias/{filename}', 'UploadController@categoryImage');
$router->get('/uploads/productos/{filename}', 'UploadController@productImage');
$router->get('/uploads/comprobantes/{filename}', 'UploadController@voucher');

// ─── Autenticación ────────────────────────────────────────────────────────────
$router->get('/registro', 'AuthController@registroForm');
$router->post('/registro', 'AuthController@registro', ['csrf']);
$router->get('/verificar/{codigo}', 'AuthController@verificar');
$router->post('/verificar/check', 'AuthController@verificarCodigo', ['csrf']);
$router->post('/auth/reenviar', 'AuthController@reenviarCodigo', ['csrf']);
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login', ['csrf']);
$router->get('/logout', 'AuthController@logout', ['auth']);
$router->get('/recuperar', 'AuthController@recuperarForm');
$router->post('/recuperar', 'AuthController@recuperar', ['csrf']);
$router->get('/restablecer/{token}', 'AuthController@restablecerForm');
$router->post('/restablecer/{token}', 'AuthController@restablecer', ['csrf']);

// ─── Google OAuth ─────────────────────────────────────────────────────────────
$router->get('/auth/google', 'OAuthController@redirectToGoogle');
$router->get('/auth/google/callback', 'OAuthController@handleCallback');

// ─── Carrito ──────────────────────────────────────────────────────────────────
$router->get('/carrito', 'CartController@index');
$router->post('/carrito/agregar', 'CartController@add', ['csrf']);
$router->post('/carrito/actualizar', 'CartController@update', ['csrf']);
$router->post('/carrito/eliminar', 'CartController@remove', ['csrf']);
$router->post('/carrito/vaciar', 'CartController@clear', ['csrf']);

// ─── Cupones ──────────────────────────────────────────────────────────────────
$router->post('/cupon/aplicar', 'CouponController@apply', ['csrf']);
$router->post('/cupon/quitar', 'CouponController@remove', ['csrf']);

// ─── Checkout ─────────────────────────────────────────────────────────────────
$router->get('/checkout', 'CheckoutController@index', ['auth']);
$router->post('/checkout/subir-comprobante', 'CheckoutController@uploadVoucher', ['auth', 'csrf']);
$router->post('/checkout/confirmar', 'CheckoutController@confirm', ['auth', 'csrf']);
$router->get('/checkout/confirmacion/{orderId}', 'CheckoutController@confirmation', ['auth']);

// ─── Dashboard Cliente ────────────────────────────────────────────────────────
$router->get('/mi-cuenta', 'ClientController@dashboard', ['auth']);
$router->get('/mi-cuenta/perfil', 'ClientController@perfil', ['auth']);
$router->post('/mi-cuenta/perfil', 'ClientController@updatePerfil', ['auth', 'csrf']);
$router->get('/mi-cuenta/contrasena', 'ClientController@contrasena', ['auth']);
$router->post('/mi-cuenta/contrasena', 'ClientController@updateContrasena', ['auth', 'csrf']);
$router->get('/mi-cuenta/ordenes', 'ClientController@ordenes', ['auth']);
$router->get('/mi-cuenta/ordenes/{id}', 'ClientController@ordenDetalle', ['auth']);
$router->get('/mi-cuenta/wishlist', 'ClientController@wishlist', ['auth']);

// ─── Wishlist ─────────────────────────────────────────────────────────────────
$router->post('/wishlist/toggle', 'WishlistController@toggle', ['auth', 'csrf']);

// ─── Newsletter ───────────────────────────────────────────────────────────────
$router->post('/newsletter/suscribir', 'NewsletterController@subscribe', ['csrf']);

// ─── Panel Administrador ──────────────────────────────────────────────────────
$router->get('/admin', 'Admin\DashboardController@index', ['auth', 'admin']);

// Usuarios Admin
$router->get('/admin/usuarios', 'Admin\UserController@index', ['auth', 'admin']);
$router->get('/admin/usuarios/{id}', 'Admin\UserController@show', ['auth', 'admin']);
$router->post('/admin/usuarios/{id}/toggle', 'Admin\UserController@toggle', ['auth', 'admin', 'csrf']);

// Categorías Admin
$router->get('/admin/categorias', 'Admin\CategoryController@index', ['auth', 'admin']);
$router->get('/admin/categorias/crear', 'Admin\CategoryController@create', ['auth', 'admin']);
$router->post('/admin/categorias/crear', 'Admin\CategoryController@store', ['auth', 'admin', 'csrf']);
$router->get('/admin/categorias/{id}/editar', 'Admin\CategoryController@edit', ['auth', 'admin']);
$router->post('/admin/categorias/{id}/editar', 'Admin\CategoryController@update', ['auth', 'admin', 'csrf']);
$router->post('/admin/categorias/{id}/toggle', 'Admin\CategoryController@toggle', ['auth', 'admin', 'csrf']);

// Productos Admin
$router->get('/admin/productos', 'Admin\ProductController@index', ['auth', 'admin']);
$router->get('/admin/productos/crear', 'Admin\ProductController@create', ['auth', 'admin']);
$router->post('/admin/productos/crear', 'Admin\ProductController@store', ['auth', 'admin', 'csrf']);
$router->get('/admin/productos/{id}/editar', 'Admin\ProductController@edit', ['auth', 'admin']);
$router->post('/admin/productos/{id}/editar', 'Admin\ProductController@update', ['auth', 'admin', 'csrf']);
$router->post('/admin/productos/{id}/toggle', 'Admin\ProductController@toggle', ['auth', 'admin', 'csrf']);
$router->post('/admin/productos/{id}/imagen/eliminar', 'Admin\ProductController@deleteImage', ['auth', 'admin', 'csrf']);

// Órdenes Admin
$router->get('/admin/ordenes', 'Admin\OrderController@index', ['auth', 'admin']);
$router->get('/admin/ordenes/{id}', 'Admin\OrderController@show', ['auth', 'admin']);
$router->post('/admin/ordenes/{id}/estado', 'Admin\OrderController@updateStatus', ['auth', 'admin', 'csrf']);

// Pagos Admin
$router->get('/admin/pagos', 'Admin\PaymentController@index', ['auth', 'admin']);
$router->post('/admin/pagos/aprobar', 'Admin\PaymentController@approve', ['auth', 'admin', 'csrf']);
$router->post('/admin/pagos/rechazar', 'Admin\PaymentController@reject', ['auth', 'admin', 'csrf']);

// Cupones Admin
$router->get('/admin/cupones', 'Admin\CouponController@index', ['auth', 'admin']);
$router->get('/admin/cupones/crear', 'Admin\CouponController@create', ['auth', 'admin']);
$router->post('/admin/cupones/crear', 'Admin\CouponController@store', ['auth', 'admin', 'csrf']);
$router->get('/admin/cupones/{id}/editar', 'Admin\CouponController@edit', ['auth', 'admin']);
$router->post('/admin/cupones/{id}/editar', 'Admin\CouponController@update', ['auth', 'admin', 'csrf']);
$router->post('/admin/cupones/{id}/toggle', 'Admin\CouponController@toggle', ['auth', 'admin', 'csrf']);

// Newsletter Admin
$router->get('/admin/newsletter', 'Admin\NewsletterController@index', ['auth', 'admin']);

// Configuración Admin
$router->get('/admin/configuracion', 'Admin\ConfigController@index', ['auth', 'admin']);
$router->post('/admin/configuracion', 'Admin\ConfigController@update', ['auth', 'admin', 'csrf']);
