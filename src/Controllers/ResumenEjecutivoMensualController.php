<?php
namespace App\Controllers;

use App\Models\Proyecto;

class ResumenEjecutivoMensualController {

    public function index() {
        $usuario_id = $_SESSION['id'] ?? null;
        $categorias = Proyecto::getCategorias($usuario_id);

        $anio = (int)date('Y');
        $resumen = Proyecto::getResumenEjecutivoMensual($anio, $usuario_id);

        extract([
            'categorias' => $categorias,
            'resumen' => $resumen,
            'anio' => $anio
        ]);

        require_once __DIR__ . '/../Views/ResumenEjecutivoMensual/index.php';
    }

    public static function listar() {
        $usuario_id = $_SESSION['id'] ?? null;

        $anio = (int)($_GET['anio'] ?? date('Y'));
        $categoria = trim((string)($_GET['categoria'] ?? ''));
        $estado = trim((string)($_GET['estado'] ?? ''));

        $data = Proyecto::getResumenEjecutivoMensual($anio, $usuario_id, $categoria, $estado);

        return [
            'success' => true,
            'filtros' => [
                'anio' => $anio,
                'categoria' => $categoria,
                'estado' => $estado,
                'periodo' => (string)$anio
            ],
            'data' => $data
        ];
    }
}
