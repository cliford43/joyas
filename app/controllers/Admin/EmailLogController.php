<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\EmailLogModel;

class EmailLogController extends Controller
{
    /**
     * Muestra la bitácora de correos enviados con paginación.
     */
    public function index(): void
    {
        $page  = max(1, (int)($_GET['page'] ?? 1));
        $limit = 30;
        $offset = ($page - 1) * $limit;

        $correos    = EmailLogModel::findAll($limit, $offset);
        $total      = EmailLogModel::countAll();
        $totalPages = max(1, (int)ceil($total / $limit));

        $this->render('admin/correos/index', [
            'pageTitle'   => 'Bitácora de Correos',
            'currentPage' => 'correos-log',
            'correos'     => $correos,
            'page'        => $page,
            'totalPages'  => $totalPages,
            'total'       => $total,
        ], 'admin_layout');
    }
}
