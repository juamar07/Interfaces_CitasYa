<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class EstadoCita extends BaseModel
{
    public static function idPorNombre(string $nombre): ?int
    {
        $stmt = self::db()->prepare('SELECT id FROM estado_cita WHERE nombre = :nombre LIMIT 1');
        $stmt->bindValue(':nombre', $nombre);
        $stmt->execute();
        $value = $stmt->fetchColumn();
        return $value !== false ? (int) $value : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function todos(): array
    {
        $stmt = self::db()->query('SELECT * FROM estado_cita ORDER BY id');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
