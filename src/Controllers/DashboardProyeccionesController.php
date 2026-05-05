<?php
namespace App\Controllers;

use App\Models\DashboardProyecciones;

class DashboardProyeccionesController
{
    public function index()
    {
        require_once __DIR__ . '/../Views/DashboardProyecciones/index.php';
    }

    public static function data($filters = [])
    {
        return DashboardProyecciones::getDashboardData($filters);
    }
}
