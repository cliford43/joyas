<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\UserModel;
use Services\GoogleOAuth;

/**
 * OAuthController — Autenticación con Google OAuth 2.0.
 */
class OAuthController extends Controller
{
    /** GET /auth/google — Redirige a Google */
    public function redirectToGoogle(): void
    {
        $oauth = new GoogleOAuth();
        $url   = $oauth->getAuthorizationUrl();
        $this->redirect($url);
    }

    /** GET /auth/google/callback — Procesa el callback de Google */
    public function handleCallback(): void
    {
        $code  = $_GET['code']  ?? '';
        $state = $_GET['state'] ?? '';
        $error = $_GET['error'] ?? '';

        // Error retornado por Google
        if (!empty($error)) {
            $this->flash('error', 'Autenticación cancelada.');
            $this->redirect(url('login'));
            return;
        }

        // Validar state para prevenir CSRF
        $oauth = new GoogleOAuth();
        if (!$oauth->validateState($state)) {
            $this->flash('error', 'Estado inválido. Intenta de nuevo.');
            $this->redirect(url('login'));
            return;
        }

        if (empty($code)) {
            $this->flash('error', 'No se recibió el código de autorización.');
            $this->redirect(url('login'));
            return;
        }

        try {
            $googleUser = $oauth->getUserFromCode($code);
        } catch (\RuntimeException $e) {
            $this->flash('error', 'Error al conectar con Google. Intenta de nuevo.');
            $this->redirect(url('login'));
            return;
        }

        if (empty($googleUser['email'])) {
            $this->flash('error', 'No se pudo obtener el correo de Google.');
            $this->redirect(url('login'));
            return;
        }

        // Buscar usuario por Google ID primero, luego por correo
        $user = UserModel::findByGoogleId($googleUser['id'])
             ?? UserModel::findByEmail($googleUser['email']);

        if (!$user) {
            // Crear nueva cuenta automáticamente
            $userId = UserModel::createFromGoogle([
                'nombre'    => $googleUser['nombre'],
                'apellido'  => $googleUser['apellido'],
                'correo'    => $googleUser['email'],
                'google_id' => $googleUser['id'],
            ]);
            $user = UserModel::findById($userId);
        }

        if (!$user) {
            $this->flash('error', 'No se pudo crear la cuenta. Intenta de nuevo.');
            $this->redirect(url('login'));
            return;
        }

        // Iniciar sesión
        session_regenerate_id(true);
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['user_rol']    = $user['rol'];
        $_SESSION['user_nombre'] = $user['nombre'];

        $redirect = $_SESSION['redirect_after_login'] ?? url('mi-cuenta');
        unset($_SESSION['redirect_after_login']);

        $this->redirect($redirect);
    }
}
