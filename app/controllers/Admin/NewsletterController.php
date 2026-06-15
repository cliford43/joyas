<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\NewsletterModel;

class NewsletterController extends Controller
{
    public function index(): void
    {
        $suscriptores = NewsletterModel::findAll();
        $this->render('admin/newsletter/index', [
            'pageTitle'    => 'Newsletter',
            'currentPage'  => 'newsletter',
            'suscriptores' => $suscriptores,
        ], 'admin_layout');
    }
}
