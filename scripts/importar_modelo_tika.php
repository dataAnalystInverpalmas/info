<?php
declare(strict_types=1);

/**
 * CLI para importar uno o varios modelos Tika desde la ruta de red a MySQL.
 *
 * Uso:
 *   php scripts/importar_modelo_tika.php 547 548
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script solo puede ejecutarse por línea de comandos (CLI).');
}

require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/funciones/conexion.php';

use App\Models\ComparadorTika;

$args = array_slice($argv, 1);
$ids = [];
foreach ($args as $arg) {
    foreach (preg_split('/[\s,;]+/', trim($arg)) as $part) {
        if ($part !== '' && ctype_digit($part) && (int)$part > 0) {
            $ids[] = (int)$part;
        }
    }
}
$ids = array_values(array_unique($ids));

if (empty($ids)) {
    fwrite(STDERR, "Uso: php scripts/importar_modelo_tika.php <ID> [ID ...]\nEjemplo: php scripts/importar_modelo_tika.php 547 548\n");
    exit(1);
}

$exitCode = 0;
$usuarioCli = get_current_user() ?: 'cli';

foreach ($ids as $id) {
    $t0 = microtime(true);
    echo "Importando modelo {$id}...\n";
    try {
        $res = ComparadorTika::importFromNetwork($id, $usuarioCli);
        $elapsed = round(microtime(true) - $t0, 2);
        $tot = $res['totales'];
        echo sprintf(
            "✓ %d: %s (%.2fs)\n    esquejes %s · producción %s · guarde %s · salvamento %s\n",
            $res['id'],
            $res['escenario'],
            $elapsed,
            number_format($tot['esquejes'], 0, ',', '.'),
            number_format($tot['produccion'], 0, ',', '.'),
            number_format($tot['guarde'], 0, ',', '.'),
            number_format($tot['salvamento'], 0, ',', '.')
        );
    } catch (\Throwable $e) {
        fwrite(STDERR, "✗ Error en modelo {$id}: " . $e->getMessage() . "\n");
        $exitCode = 1;
    }
}

exit($exitCode);

