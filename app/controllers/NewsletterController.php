<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\NewsletterModel;

class NewsletterController extends Controller
{
    /** POST /newsletter/suscribir */
    public function subscribe(): void
    {
        $correo = strtolower(trim($_POST['correo'] ?? ''));

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $msg = ['success' => false, 'message' => 'Correo electrónico inválido.'];
        } elseif (NewsletterModel::exists($correo)) {
            $msg = ['success' => true, 'message' => 'Este correo ya está suscrito.'];
        } else {
            NewsletterModel::subscribe($correo);
            $msg = ['success' => true, 'message' => '¡Suscrito exitosamente! Gracias.'];
        }

        if ($this->isAjax()) {
            $this->json($msg);
            return;
        }

        $this->flash($msg['success'] ? 'success' : 'error', $msg['message']);
        $this->redirect(url());
    }
}
