<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Compra extends BaseModel
{
    public static function crear(array $data): int
    {
        $sql = 'INSERT INTO compras (negocio_id, metodo_id, estado_id, tokens, monto_cop, ref_externa)
                VALUES (:negocio, :metodo, :estado, :tokens, :monto, :ref)';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':negocio', $data['negocio_id'], PDO::PARAM_INT);
        $stmt->bindValue(':metodo', $data['metodo_id'], PDO::PARAM_INT);
        $stmt->bindValue(':estado', $data['estado_id'], PDO::PARAM_INT);
        $stmt->bindValue(':tokens', $data['tokens']);
        $stmt->bindValue(':monto', $data['monto_cop']);
        $stmt->bindValue(':ref', $data['ref_externa']);
        $stmt->execute();
        return (int) self::db()->lastInsertId();
    }

    public static function actualizarEstadoPorReferencia(string $ref, int $estadoId): void
    {
        $stmt = self::db()->prepare('UPDATE compras SET estado_id = :estado WHERE ref_externa = :ref');
        $stmt->bindValue(':estado', $estadoId, PDO::PARAM_INT);
        $stmt->bindValue(':ref', $ref);
        $stmt->execute();
    }
}
