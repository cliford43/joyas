<?php

namespace App\Controllers;

use Core\Controller;
use Core\Middleware\CsrfMiddleware;
use App\Models\UserModel;
use Services\Mailer;
use Services\NotificationService;

/**
 * AuthController — Registro, login, verificación y recuperación de contraseña.
 */
class AuthController extends Controller
{
    // ─── Registro ─────────────────────────────────────────────

    public function registroForm(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect(url('mi-cuenta'));
        }
        $this->render('auth/registro', ['pageTitle' => 'Crear cuenta']);
    }

    public function registro(): void
    {
        $nombre    = trim($_POST['nombre']    ?? '');
        $apellido  = trim($_POST['apellido']  ?? '');
        $correo    = strtolower(trim($_POST['correo'] ?? ''));
        $password  = $_POST['password']  ?? '';
        $password2 = $_POST['password2'] ?? '';

        $errors = [];

        if (mb_strlen($nombre) < 2)   $errors[] = 'El nombre debe tener al menos 2 caracteres.';
        if (mb_strlen($apellido) < 2) $errors[] = 'El apellido debe tener al menos 2 caracteres.';
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo electrónico inválido.';
        if (mb_strlen($password) < 8) $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        if ($password !== $password2)  $errors[] = 'Las contraseñas no coinciden.';
        if (UserModel::emailExists($correo)) $errors[] = 'Este correo ya está registrado.';

        if (!empty($errors)) {
            $this->render('auth/registro', [
                'pageTitle' => 'Crear cuenta',
                'errors'    => $errors,
                'old'       => ['nombre' => $nombre, 'apellido' => $apellido, 'correo' => $correo],
            ]);
            return;
        }

        $userId = UserModel::create([
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'correo'   => $correo,
            'password' => $password,
        ]);

        // Obtener código generado
        $user = UserModel::findById($userId);
        $code = $user['codigo_verificacion'] ?? '';

        // Enviar correo de verificación
        $mailer = new Mailer();
        $mailer->send(
            $correo,
            'Verifica tu cuenta VILUNA',
            'verification',
            [
                'nombre'      => $nombre,
                'codigo'      => $code,
                'verificarUrl'=> url("verificar/{$code}?email={$correo}"),
            ]
        );

        // Enviar correo de bienvenida y notificar al admin
        $userData = ['nombre' => $nombre, 'apellido' => $apellido, 'correo' => $correo];
        NotificationService::welcomeEmail($userData);
        NotificationService::adminNewUser($userData);

        // Guardar en sesión para el formulario de verificación
        $_SESSION['verificar_email'] = $correo;

        $this->redirect(url('verificar/pending'));
    }

    // ─── Verificación de cuenta ───────────────────────────────

    public function verificar(string $codigo = ''): void
    {
        // Pantalla de espera (pendiente de verificación)
        if ($codigo === 'pending') {
            $this->render('auth/verificar', ['pageTitle' => 'Verifica tu correo']);
            return;
        }

        $email = trim($_GET['email'] ?? ($_SESSION['verificar_email'] ?? ''));

        if (empty($email) || empty($codigo)) {
            $this->flash('error', 'Enlace de verificación inválido.');
            $this->redirect(url('login'));
            return;
        }

        $ok = UserModel::verifyByEmail($email, $codigo);

        if ($ok) {
            $this->flash('success', '¡Cuenta verificada! Ya puedes iniciar sesión.');
            $this->redirect(url('login'));
        } else {
            $this->render('auth/verificar', [
                'pageTitle' => 'Verificar cuenta',
                'error'     => 'Código inválido o cuenta ya verificada.',
                'email'     => $email,
            ]);
        }
    }

    /** POST /verificar/check */
    public function verificarCodigo(): void
    {
        $email  = strtolower(trim($_POST['email'] ?? ($_SESSION['verificar_email'] ?? '')));
        $codigo = trim($_POST['codigo'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $codigo === '') {
            $this->render('auth/verificar', [
                'pageTitle' => 'Verificar cuenta',
                'error'     => 'Correo o código inválido.',
                'email'     => $email,
            ]);
            return;
        }

        $ok = UserModel::verifyByEmail($email, $codigo);

        if ($ok) {
            unset($_SESSION['verificar_email']);
            $this->flash('success', '¡Cuenta verificada! Ya puedes iniciar sesión.');
            $this->redirect(url('login'));
            return;
        }

        $this->render('auth/verificar', [
            'pageTitle' => 'Verificar cuenta',
            'error'     => 'Código inválido o cuenta ya verificada.',
            'email'     => $email,
        ]);
    }

    // ─── Login ────────────────────────────────────────────────

    public function loginForm(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect(url('mi-cuenta'));
        }
        $this->render('auth/login', ['pageTitle' => 'Iniciar sesión']);
    }

    public function login(): void
    {
        $correo   = strtolower(trim($_POST['correo']   ?? ''));
        $password = $_POST['password'] ?? '';

        $errors = [];

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo inválido.';
        if (empty($password)) $errors[] = 'Ingresa tu contraseña.';

        if (!empty($errors)) {
            $this->render('auth/login', ['pageTitle' => 'Iniciar sesión', 'errors' => $errors, 'old' => ['correo' => $correo]]);
            return;
        }

        $user = UserModel::findByEmail($correo);

        if (!$user || !password_verify($password, (string)($user['password'] ?? ''))) {
            $this->render('auth/login', [
                'pageTitle' => 'Iniciar sesión',
                'errors'    => ['Correo o contraseña incorrectos.'],
                'old'       => ['correo' => $correo],
            ]);
            return;
        }

        if (!(int)$user['verificado']) {
            $_SESSION['verificar_email'] = $correo;
            $this->render('auth/login', [
                'pageTitle'       => 'Iniciar sesión',
                'errors'          => ['Debes verificar tu correo antes de ingresar.'],
                'showResend'      => true,
                'resendEmail'     => $correo,
                'old'             => ['correo' => $correo],
            ]);
            return;
        }

        // Regenerar ID de sesión para prevenir session fixation
        session_regenerate_id(true);

        $_SESSION['user_id']  = $user['id'];
        $_SESSION['user_rol'] = $user['rol'];
        $_SESSION['user_nombre'] = $user['nombre'];

        $redirect = $_SESSION['redirect_after_login'] ?? url('mi-cuenta');
        unset($_SESSION['redirect_after_login']);

        $this->redirect($redirect);
    }

    // ─── Logout ───────────────────────────────────────────────

    public function logout(): void
    {
        // Destruir sesión completamente
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        $this->redirect(url('login'));
    }

    // ─── Recuperar contraseña ─────────────────────────────────

    public function recuperarForm(): void
    {
        $this->render('auth/recuperar', ['pageTitle' => 'Recuperar contraseña']);
    }

    public function recuperar(): void
    {
        $correo = strtolower(trim($_POST['correo'] ?? ''));

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $this->render('auth/recuperar', [
                'pageTitle' => 'Recuperar contraseña',
                'errors'    => ['Ingresa un correo válido.'],
            ]);
            return;
        }

        $user = UserModel::findByEmail($correo);

        // Siempre mostrar el mismo mensaje para no revelar si el correo existe
        if ($user && (int)$user['verificado']) {
            $token = UserModel::setResetToken((int)$user['id']);
            $mailer = new Mailer();
            $mailer->send(
                $correo,
                'Restablece tu contraseña — VILUNA',
                'password_reset',
                [
                    'nombre'   => $user['nombre'],
                    'resetUrl' => url("restablecer/{$token}"),
                ]
            );
        }

        $this->render('auth/recuperar', [
            'pageTitle' => 'Recuperar contraseña',
            'success'   => 'Si tu correo está registrado, recibirás un enlace para restablecer tu contraseña.',
        ]);
    }

    // ─── Restablecer contraseña ───────────────────────────────

    public function restablecerForm(string $token): void
    {
        $user = UserModel::findByResetToken($token);
        if (!$user) {
            $this->flash('error', 'El enlace es inválido o ha expirado.');
            $this->redirect(url('recuperar'));
            return;
        }
        $this->render('auth/restablecer', [
            'pageTitle' => 'Nueva contraseña',
            'token'     => $token,
        ]);
    }

    public function restablecer(string $token): void
    {
        $user = UserModel::findByResetToken($token);
        if (!$user) {
            $this->flash('error', 'El enlace es inválido o ha expirado.');
            $this->redirect(url('recuperar'));
            return;
        }

        $password  = $_POST['password']  ?? '';
        $password2 = $_POST['password2'] ?? '';
        $errors    = [];

        if (mb_strlen($password) < 8)  $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        if ($password !== $password2)   $errors[] = 'Las contraseñas no coinciden.';

        if (!empty($errors)) {
            $this->render('auth/restablecer', [
                'pageTitle' => 'Nueva contraseña',
                'token'     => $token,
                'errors'    => $errors,
            ]);
            return;
        }

        UserModel::forceChangePassword((int)$user['id'], $password);
        UserModel::clearResetToken((int)$user['id']); // Invalidar tras primer uso

        $this->flash('success', 'Contraseña actualizada. Ya puedes iniciar sesión.');
        $this->redirect(url('login'));
    }

    // ─── Reenviar código de verificación ─────────────────────

    public function reenviarCodigo(): void
    {
        $correo = strtolower(trim($_POST['correo'] ?? ''));
        $user   = UserModel::findByEmail($correo);

        if ($user && !(int)$user['verificado']) {
            $code = UserModel::regenerateVerificationCode((int)$user['id']);
            $mailer = new Mailer();
            $mailer->send(
                $correo,
                'Nuevo código de verificación — VILUNA',
                'verification',
                [
                    'nombre'      => $user['nombre'],
                    'codigo'      => $code,
                    'verificarUrl'=> url("verificar/{$code}?email={$correo}"),
                ]
            );
        }

        $this->flash('success', 'Si la cuenta existe, se envió un nuevo código.');
        $this->redirect(url('verificar/pending'));
    }
}
