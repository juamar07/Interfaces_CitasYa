<?php
declare(strict_types=1);

require __DIR__ . '/../vendor_autoload.php';

use App\Config\Database;
use App\Config\Env;
use App\Models\Estadistica;

$root = dirname(__DIR__);
Env::load($root);

$pdo = Database::connection();

$negociosActivos = (int) $pdo->query('SELECT COUNT(*) FROM negocios WHERE activo = 1')->fetchColumn();
$barberosActivos = (int) $pdo->query('SELECT COUNT(*) FROM personal WHERE activo = 1')->fetchColumn();
$citasProgramadas = (int) $pdo->query('SELECT COUNT(*) FROM citas')->fetchColumn();
$citasCanceladas = (int) $pdo->query("SELECT COUNT(*) FROM citas c INNER JOIN estado_cita e ON e.id = c.estado_id WHERE e.nombre = 'cancelada'")->fetchColumn();

$tokensComprados = (int) $pdo->query('SELECT COALESCE(SUM(tokens),0) FROM compras')->fetchColumn();
$tokensConsumidos = (int) $pdo->query('SELECT COALESCE(SUM(debito),0) FROM movimientos_tokens')->fetchColumn();
$tokensSaldo = (int) $pdo->query('SELECT COALESCE(SUM(tokens),0) FROM negocios')->fetchColumn();

$comentariosTotal = (int) $pdo->query('SELECT COUNT(*) FROM comentarios')->fetchColumn();
$califPromedio = (float) $pdo->query('SELECT COALESCE(AVG(calificacion),0) FROM comentarios')->fetchColumn();

$califDistribucion = [];
for ($i = 1; $i <= 5; $i++) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM comentarios WHERE calificacion = :calificacion');
    $stmt->execute([':calificacion' => $i]);
    $califDistribucion[$i] = (int) $stmt->fetchColumn();
}

$recomiendanSi = (int) $pdo->query('SELECT COUNT(*) FROM comentarios WHERE recomienda = 1')->fetchColumn();
$recomiendanNo = (int) $pdo->query('SELECT COUNT(*) FROM comentarios WHERE recomienda = 0')->fetchColumn();

$payload = [
    'alcance' => 'pagina',
    'audiencia' => 'solo_admin',
    'negocio' => null,
    'inicio' => date('Y-m-d'),
    'fin' => date('Y-m-d'),
    'negocios_activos' => $negociosActivos,
    'barberos_activos' => $barberosActivos,
    'citas_programadas' => $citasProgramadas,
    'citas_canceladas' => $citasCanceladas,
    'prom_citas_7d' => 0,
    'tasa_cancelacion' => $citasProgramadas > 0 ? round(($citasCanceladas / $citasProgramadas) * 100, 2) : 0,
    'tokens_comprados' => $tokensComprados,
    'tokens_consumidos' => $tokensConsumidos,
    'tokens_saldo_total' => $tokensSaldo,
    'suscrip_activas' => (int) $pdo->query('SELECT COUNT(*) FROM suscripciones_plus WHERE activa = 1')->fetchColumn(),
    'suscrip_nuevas' => 0,
    'comentarios_total' => $comentariosTotal,
    'calif_promedio' => round($califPromedio, 2),
    'calif_5' => $califDistribucion[5],
    'calif_4' => $califDistribucion[4],
    'calif_3' => $califDistribucion[3],
    'calif_2' => $califDistribucion[2],
    'calif_1' => $califDistribucion[1],
    'recomiendan_si' => $recomiendanSi,
    'recomiendan_no' => $recomiendanNo,
    'observaciones' => 'Snapshot generado por cron.',
];

Estadistica::crear($payload);

echo "Snapshot generado\n";
