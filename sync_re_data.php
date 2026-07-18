<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(1800); // 30 minutes just in case

$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

echo "Starting memory-efficient, prepared-once synchronization of RE data...\n";

// Find all positive real records in quipus_vs_proy along with their computed date
// We do a LEFT JOIN to find which ones are missing from ld_proyecciones WHERE tipo = 'RE'
$sql_missing = "
    SELECT 
        q.finca, 
        q.flor, 
        q.bloque, 
        q.cosecha, 
        q.variedad, 
        q.sem_prod, 
        q.`inverpalmas-real` as real_tallos, 
        q.color,
        STR_TO_DATE(CONCAT('20', FLOOR(q.sem_prod/100), ' ', q.sem_prod % 100, ' Monday'), '%Y %u %W') as computed_fecha
    FROM quipus_vs_proy q
    LEFT JOIN ld_proyecciones p ON p.finca = q.finca 
                                AND p.bloque = q.bloque 
                                AND p.variedad = q.variedad 
                                AND p.cosecha = q.cosecha 
                                AND p.fecha = STR_TO_DATE(CONCAT('20', FLOOR(q.sem_prod/100), ' ', q.sem_prod % 100, ' Monday'), '%Y %u %W')
                                AND p.tipo = 'RE'
    WHERE q.`inverpalmas-real` IS NOT NULL 
      AND q.`inverpalmas-real` > 0 
      AND p.finca IS NULL
    GROUP BY q.finca, q.flor, q.bloque, q.cosecha, q.variedad, q.sem_prod, q.`inverpalmas-real`, q.color, computed_fecha
";

$res_missing = $conn->query($sql_missing);
if (!$res_missing) {
    die("Failed to select missing records: " . $conn->error . "\n");
}

$missing_records = [];
while ($row = $res_missing->fetch_assoc()) {
    $missing_records[] = $row;
}
$res_missing->free();

$total_missing = count($missing_records);
echo "Found " . $total_missing . " missing RE rows to synchronize.\n";

if ($total_missing === 0) {
    echo "Nothing to synchronize. All rows are already up-to-date.\n";
    $conn->close();
    exit;
}

// Prepare statements ONCE outside the loop
$ins_stmt = $conn->prepare("INSERT INTO ld_proyecciones (finca, flor, bloque, variedad, cosecha, fecha, matas, edad, ncolor, tipo, tallos) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'RE', ?)");
if (!$ins_stmt) {
    die("Failed to prepare INSERT statement: " . $conn->error . "\n");
}

$stmt_meta = $conn->prepare("SELECT matas, edad, ncolor, flor FROM ld_proyecciones WHERE finca=? AND bloque=? AND variedad=? AND cosecha=? AND fecha=? AND tipo IN ('PT', 'IN', 'AJ') LIMIT 1");
if (!$stmt_meta) {
    die("Failed to prepare metadata SELECT: " . $conn->error . "\n");
}

$stmt_ref = $conn->prepare("SELECT fecha, matas, edad, ncolor, flor FROM ld_proyecciones WHERE finca=? AND bloque=? AND variedad=? AND cosecha=? ORDER BY ABS(DATEDIFF(fecha, ?)) LIMIT 1");
if (!$stmt_ref) {
    die("Failed to prepare reference SELECT: " . $conn->error . "\n");
}

// Bind variables for the prepared statements
$finca = '';
$flor = '';
$bloque = 0;
$variedad = '';
$cosecha = '';
$fecha_str = '';
$matas = 0;
$edad = 0;
$ncolor = '';
$tallos = 0.0;

$ins_stmt->bind_param("ssisssdisd", $finca, $flor, $bloque, $variedad, $cosecha, $fecha_str, $matas, $edad, $ncolor, $tallos);

$meta_finca = '';
$meta_bloque = 0;
$meta_variedad = '';
$meta_cosecha = '';
$meta_fecha = '';

$stmt_meta->bind_param("sisss", $meta_finca, $meta_bloque, $meta_variedad, $meta_cosecha, $meta_fecha);

$ref_finca = '';
$ref_bloque = 0;
$ref_variedad = '';
$ref_cosecha = '';
$ref_fecha = '';

$stmt_ref->bind_param("sisss", $ref_finca, $ref_bloque, $ref_variedad, $ref_cosecha, $ref_fecha);

$inserted = 0;
$conn->begin_transaction();

$start_time = microtime(true);

foreach ($missing_records as $idx => $row) {
    $finca = $row['finca'];
    $flor = $row['flor'] ?: 'CLA';
    $bloque = intval($row['bloque']);
    $variedad = $row['variedad'];
    $cosecha = $row['cosecha'];
    $fecha_str = $row['computed_fecha'];
    $tallos = (float)$row['real_tallos'];
    $color = $row['color'];

    $matas = 0;
    $edad = 0;
    $ncolor = $color;

    // 1. Try exact metadata match
    $meta_finca = $finca;
    $meta_bloque = $bloque;
    $meta_variedad = $variedad;
    $meta_cosecha = $cosecha;
    $meta_fecha = $fecha_str;

    $stmt_meta->execute();
    $res_meta = $stmt_meta->get_result();

    if ($res_meta && $res_meta->num_rows > 0) {
        $meta = $res_meta->fetch_assoc();
        $matas = $meta['matas'];
        $edad = $meta['edad'];
        $ncolor = $meta['ncolor'] ?: $color;
        $flor = $meta['flor'] ?: $flor;
    } else {
        // 2. Extrapolate from closest date
        $ref_finca = $finca;
        $ref_bloque = $bloque;
        $ref_variedad = $variedad;
        $ref_cosecha = $cosecha;
        $ref_fecha = $fecha_str;

        $stmt_ref->execute();
        $res_ref = $stmt_ref->get_result();

        if ($res_ref && $res_ref->num_rows > 0) {
            $closest = $res_ref->fetch_assoc();
            $matas = $closest['matas'];
            $ncolor = $closest['ncolor'] ?: $color;
            $flor = $closest['flor'] ?: $flor;
            
            // Extrapolate age based on weekly diff
            $date1 = new DateTime($closest['fecha']);
            $date2 = new DateTime($fecha_str);
            $diff_weeks = round(($date2->getTimestamp() - $date1->getTimestamp()) / (7 * 86400));
            $edad = $closest['edad'] + $diff_weeks;
            if ($edad < 0) {
                $edad = 0;
            }
        }
        if ($res_ref) {
            $res_ref->free();
        }
    }
    if ($res_meta) {
        $res_meta->free();
    }

    // Insert new row
    if (!$ins_stmt->execute()) {
        echo "Error inserting row ($idx): " . $ins_stmt->error . "\n";
    } else {
        $inserted++;
    }

    // Print progress and commit in batches of 5000 to keep transaction lock footprint reasonable
    if ($inserted % 1000 === 0) {
        $elapsed = round(microtime(true) - $start_time, 2);
        echo "Processed $inserted / $total_missing rows... Elapsed: {$elapsed}s\n";
    }

    if ($inserted % 5000 === 0) {
        $conn->commit();
        $conn->begin_transaction();
    }
}

$conn->commit();

$ins_stmt->close();
$stmt_meta->close();
$stmt_ref->close();

$total_elapsed = round(microtime(true) - $start_time, 2);
echo "Synchronization completed!\n";
echo "Successfully inserted $inserted missing RE rows into ld_proyecciones in {$total_elapsed}s.\n";

$conn->close();
