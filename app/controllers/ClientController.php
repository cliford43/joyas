<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\UserModel;
use App\Models\OrderModel;
use App\Models\WishlistModel;
use App\Models\ProductModel;
use App\Models\CategoryModel;

/**
 * ClientController — Dashboard del cliente autenticado.
 */
class ClientController extends Controller
{
    /** GET /mi-cuenta */
    public function dashboard(): void
    {
        $user          = UserModel::findById($this->userId());
        $ordenes       = OrderModel::findByUser($this->userId());
        $navCategorias = CategoryModel::findActive();

        $this->render('client/dashboard', [
            'pageTitle'     => 'Mi cuenta',
            'metaRobots'    => 'noindex',
            'user'          => $user,
            'ordenes'       => $ordenes,
            'navCategorias' => $navCategorias,
        ]);
    }

    /** GET /mi-cuenta/perfil */
    public function perfil(): void
    {
        $user          = UserModel::findById($this->userId());
        $navCategorias = CategoryModel::findActive();

        $this->render('client/perfil', [
            'pageTitle'     => 'Mi perfil',
            'metaRobots'    => 'noindex',
            'user'          => $user,
            'navCategorias' => $navCategorias,
        ]);
    }

    /** POST /mi-cuenta/perfil */
    public function updatePerfil(): void
    {
        $nombre   = trim($_POST['nombre']   ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $direccion= trim($_POST['direccion']?? '');

        $errors = [];
        if (mb_strlen($nombre)   < 2) $errors[] = 'El nombre debe tener al menos 2 caracteres.';
        if (mb_strlen($apellido) < 2) $errors[] = 'El apellido debe tener al menos 2 caracteres.';

        if (!empty($errors)) {
            $navCategorias = CategoryModel::findActive();
            $this->render('client/perfil', [
                'pageTitle'     => 'Mi perfil',
                'metaRobots'    => 'noindex',
                'user'          => UserModel::findById($this->userId()),
                'errors'        => $errors,
                'old'           => compact('nombre', 'apellido', 'telefono', 'direccion'),
                'navCategorias' => $navCategorias,
            ]);
            return;
        }

        UserModel::updateProfile($this->userId(), compact('nombre', 'apellido', 'telefono', 'direccion'));
        $_SESSION['user_nombre'] = $nombre;

        $this->flash('success', 'Perfil actualizado correctamente.');
        $this->redirect(url('mi-cuenta/perfil'));
    }

    /** GET /mi-cuenta/contrasena */
    public function contrasena(): void
    {
        $navCategorias = CategoryModel::findActive();
        $this->render('client/contrasena', [
            'pageTitle'     => 'Cambiar contraseña',
            'metaRobots'    => 'noindex',
            'navCategorias' => $navCategorias,
        ]);
    }

    /** POST /mi-cuenta/contrasena */
    public function updateContrasena(): void
    {
        $actual    = $_POST['password_actual']   ?? '';
        $nueva     = $_POST['password_nueva']    ?? '';
        $confirmar = $_POST['password_confirmar']?? '';

        $errors = [];
        if (empty($actual))                      $errors[] = 'Ingresa tu contraseña actual.';
        if (mb_strlen($nueva) < 8)               $errors[] = 'La nueva contraseña debe tener al menos 8 caracteres.';
        if ($nueva !== $confirmar)               $errors[] = 'Las contraseñas no coinciden.';

        if (empty($errors)) {
            $ok = UserModel::changePassword($this->userId(), $actual, $nueva);
            if (!$ok) $errors[] = 'La contraseña actual es incorrecta.';
        }

        $navCategorias = CategoryModel::findActive();

        if (!empty($errors)) {
            $this->render('client/contrasena', [
                'pageTitle'     => 'Cambiar contraseña',
                'metaRobots'    => 'noindex',
                'errors'        => $errors,
                'navCategorias' => $navCategorias,
            ]);
            return;
        }

        $this->flash('success', 'Contraseña actualizada correctamente.');
        $this->redirect(url('mi-cuenta/contrasena'));
    }

    /** GET /mi-cuenta/ordenes */
    public function ordenes(): void
    {
        $ordenes       = OrderModel::findByUser($this->userId());
        $navCategorias = CategoryModel::findActive();

        $this->render('client/ordenes', [
            'pageTitle'     => 'Mis órdenes',
            'metaRobots'    => 'noindex',
            'ordenes'       => $ordenes,
            'navCategorias' => $navCategorias,
        ]);
    }

    /** GET /mi-cuenta/ordenes/{id} */
    public function ordenDetalle(string $id): void
    {
        $orden = OrderModel::findById((int)$id);

        if (!$orden || (int)$orden['usuario_id'] !== $this->userId()) {
            (new ErrorController())->notFound();
            return;
        }

        $items         = OrderModel::getDetails((int)$id);
        $navCategorias = CategoryModel::findActive();

        $this->render('client/orden_detalle', [
            'pageTitle'     => 'Orden #' . $id,
            'metaRobots'    => 'noindex',
            'orden'         => $orden,
            'items'         => $items,
            'navCategorias' => $navCategorias,
        ]);
    }

    /** GET /mi-cuenta/wishlist */
    public function wishlist(): void
    {
        $productos     = WishlistModel::findByUser($this->userId());
        $navCategorias = CategoryModel::findActive();

        // Agregar imagen principal a cada producto
        foreach ($productos as &$prod) {
            $img = ProductModel::getMainImage((int)$prod['id']);
            $prod['imagen_principal'] = $img['ruta'] ?? null;
        }
        unset($prod);

        $this->render('client/wishlist', [
            'pageTitle'     => 'Mi wishlist',
            'metaRobots'    => 'noindex',
            'productos'     => $productos,
            'navCategorias' => $navCategorias,
        ]);
    }
}
