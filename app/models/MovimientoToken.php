<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class MovimientoToken extends BaseModel
{
    public static function registrar(array $data): int
    {
        $sql = 'INSERT INTO movimientos_tokens (fecha, hora, negocio_id, credito, debito, compra_id, cita_id, promocion_id, cancelacion_id)
                VALUES (:fecha, :hora, :negocio, :credito, :debito, :compra, :cita, :promo, :cancelacion)';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':fecha', $data['fecha']);
        $stmt->bindValue(':hora', $data['hora']);
        $stmt->bindValue(':negocio', $data['negocio_id'], PDO::PARAM_INT);
        $stmt->bindValue(':credito', $data['credito'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':debito', $data['debito'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':compra', $data['compra_id']);
        $stmt->bindValue(':cita', $data['cita_id']);
        $stmt->bindValue(':promo', $data['promocion_id']);
        $stmt->bindValue(':cancelacion', $data['cancelacion_id']);
        $stmt->execute();
        return (int) self::db()->lastInsertId();
    }
}
