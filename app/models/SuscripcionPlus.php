<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class SuscripcionPlus extends BaseModel
{
    public static function crear(array $data): int
    {
        $sql = 'INSERT INTO suscripciones_plus (negocio_id, fecha_inicio, fecha_fin, monto_cop, activa)
                VALUES (:negocio, :inicio, :fin, :monto, :activa)';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':negocio', $data['negocio_id'], PDO::PARAM_INT);
        $stmt->bindValue(':inicio', $data['fecha_inicio']);
        $stmt->bindValue(':fin', $data['fecha_fin']);
        $stmt->bindValue(':monto', $data['monto_cop']);
        $stmt->bindValue(':activa', $data['activa'] ?? true, PDO::PARAM_BOOL);
        $stmt->execute();
        return (int) self::db()->lastInsertId();
    }
}
