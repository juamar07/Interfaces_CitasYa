<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class AdminUsuario extends BaseModel
{
    public static function obtenerPorUsuario(int $usuarioId): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM administrador WHERE usuario_id = :usuario LIMIT 1');
        $stmt->bindValue(':usuario', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public static function actualizarEstadistica(int $adminId, int $estadisticaId): void
    {
        $stmt = self::db()->prepare('UPDATE administrador SET estadisticas_id = :estadistica WHERE id = :id');
        $stmt->bindValue(':estadistica', $estadisticaId, PDO::PARAM_INT);
        $stmt->bindValue(':id', $adminId, PDO::PARAM_INT);
        $stmt->execute();
    }
}
