<?php
$start = new DateTime('2026-01-01');
$end = new DateTime('2030-12-31');
$end = $end->modify('+1 day'); // Incluir el último día

$inserts = [];
foreach (new DatePeriod($start, new DateInterval('P1D'), $end) as $date) {
    $fecha = $date->format('Y-m-d');
    $año = $date->format('Y');
    $mes = $date->format('n');
    $dia = (int)$date->format('j');
    $dia_semana = [
        'Sunday' => 'Domingo',
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado'
    ][$date->format('l')];
    $quincena = ($dia <= 15) ? 1 : 2;

    $inserts[] = "('$fecha', $año, $mes, $quincena, '$dia_semana')";
}

$sql = "INSERT INTO `fechas` (`fecha`, `año`, `mes`, `quincena`, `dia_semana`) VALUES \n" . implode(",\n", $inserts) . ";";
file_put_contents('fechas_inserts.sql', $sql);
echo "Archivo generado: fechas_inserts.sql\n";
?>