<?php
declare(strict_types=1);

require __DIR__ . '/../vendor_autoload.php';

use App\Config\Database;
use App\Config\Env;

$root = dirname(__DIR__);
Env::load($root);

$pdo = Database::connection();
$pdo->beginTransaction();

try {
    $now = date('Y-m-d H:i:s');

    $roles = ['admin', 'dueno_negocio', 'barbero', 'cliente'];
    foreach ($roles as $rol) {
        $stmt = $pdo->prepare('INSERT INTO roles (nombre, creado_en, actualizado_en) VALUES (:nombre, :creado, :actualizado) ON DUPLICATE KEY UPDATE nombre = nombre');
        $stmt->execute([':nombre' => $rol, ':creado' => $now, ':actualizado' => $now]);
    }

    $estadoCita = ['reservada', 'completada', 'cancelada', 'no_asiste', 'reagendada'];
    foreach ($estadoCita as $estado) {
        $stmt = $pdo->prepare('INSERT INTO estado_cita (nombre, creado_en, actualizado_en) VALUES (:nombre, :creado, :actualizado) ON DUPLICATE KEY UPDATE nombre = nombre');
        $stmt->execute([':nombre' => $estado, ':creado' => $now, ':actualizado' => $now]);
    }

    $metodos = ['mercado_pago', 'manual'];
    foreach ($metodos as $metodo) {
        $stmt = $pdo->prepare('INSERT INTO metodo_pago (nombre, creado_en, actualizado_en) VALUES (:nombre, :creado, :actualizado) ON DUPLICATE KEY UPDATE nombre = nombre');
        $stmt->execute([':nombre' => $metodo, ':creado' => $now, ':actualizado' => $now]);
    }

    $estadoPago = ['pendiente', 'pagado', 'fallido', 'reembolsado'];
    foreach ($estadoPago as $estado) {
        $stmt = $pdo->prepare('INSERT INTO estado_pago (nombre, creado_en, actualizado_en) VALUES (:nombre, :creado, :actualizado) ON DUPLICATE KEY UPDATE nombre = nombre');
        $stmt->execute([':nombre' => $estado, ':creado' => $now, ':actualizado' => $now]);
    }

    $dias = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo',
    ];
    foreach ($dias as $numero => $nombre) {
        $stmt = $pdo->prepare('INSERT INTO dias_semana (id, nombre, creado_en, actualizado_en) VALUES (:id, :nombre, :creado, :actualizado) ON DUPLICATE KEY UPDATE nombre = VALUES(nombre)');
        $stmt->execute([
            ':id' => $numero,
            ':nombre' => $nombre,
            ':creado' => $now,
            ':actualizado' => $now,
        ]);
    }

    $tiposComentario = ['pagina', 'negocio'];
    foreach ($tiposComentario as $tipo) {
        $stmt = $pdo->prepare('INSERT INTO tipo_comentario (nombre, creado_en, actualizado_en) VALUES (:nombre, :creado, :actualizado) ON DUPLICATE KEY UPDATE nombre = nombre');
        $stmt->execute([':nombre' => $tipo, ':creado' => $now, ':actualizado' => $now]);
    }

    $usuariosDemo = [
        ['nombre_completo' => 'Cliente Demo', 'correo' => 'cliente@demo.test', 'telefono' => '3000000001', 'usuario' => 'usuario', 'password' => 'usuario', 'rol' => 'cliente'],
        ['nombre_completo' => 'Barbero Demo', 'correo' => 'barbero@demo.test', 'telefono' => '3000000002', 'usuario' => 'barbero', 'password' => 'barbero', 'rol' => 'barbero'],
        ['nombre_completo' => 'Admin Demo', 'correo' => 'admin@demo.test', 'telefono' => '3000000003', 'usuario' => 'admin', 'password' => 'admin', 'rol' => 'admin'],
    ];

    foreach ($usuariosDemo as $usuario) {
        $rolId = (int) $pdo->query("SELECT id FROM roles WHERE nombre = '" . $usuario['rol'] . "' LIMIT 1")->fetchColumn();
        if ($rolId === 0) {
            continue;
        }

        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE correo = :correo OR usuario = :usuario LIMIT 1');
        $stmt->execute([':correo' => $usuario['correo'], ':usuario' => $usuario['usuario']]);
        if ($stmt->fetchColumn() !== false) {
            continue;
        }

        $insert = $pdo->prepare('INSERT INTO usuarios (nombre_completo, correo, telefono, usuario, hash_contrasena, rol_id, activo, creado_en, actualizado_en) VALUES (:nombre, :correo, :telefono, :usuario, :hash, :rol, 1, :creado, :actualizado)');
        $insert->execute([
            ':nombre' => $usuario['nombre_completo'],
            ':correo' => $usuario['correo'],
            ':telefono' => $usuario['telefono'],
            ':usuario' => $usuario['usuario'],
            ':hash' => password_hash($usuario['password'], PASSWORD_BCRYPT),
            ':rol' => $rolId,
            ':creado' => $now,
            ':actualizado' => $now,
        ]);
    }

    $pdo->commit();
    echo "Seed completado\n";
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
