<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;

/**
 * Admin\DashboardController — Panel de estadísticas del administrador.
 */
class DashboardController extends Controller
{
    public function index(): void
    {
        $totalVentas     = OrderModel::getTotalSales();
        $totalUsuarios   = UserModel::count();
        $totalProductos  = ProductModel::countActive();
        $masVendidos     = ProductModel::getBestsellers(5);
        $ventasMensuales = OrderModel::getMonthlySales();
        $ordenesRecientes= OrderModel::findAll('', 5, 0);

        // Preparar datos para Chart.js
        $meses  = array_keys($ventasMensuales);
        $ventas = array_values($ventasMensuales);

        $this->render('admin/dashboard', [
            'pageTitle'       => 'Dashboard',
            'currentPage'     => 'dashboard',
            'totalVentas'     => $totalVentas,
            'totalUsuarios'   => $totalUsuarios,
            'totalProductos'  => $totalProductos,
            'masVendidos'     => $masVendidos,
            'meses'           => $meses,
            'ventas'          => $ventas,
            'ordenesRecientes'=> $ordenesRecientes,
        ], 'admin_layout');
    }
}
