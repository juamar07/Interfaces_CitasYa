<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Rol extends BaseModel
{
    public static function findByNombre(string $nombre): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM roles WHERE nombre = :nombre LIMIT 1');
        $stmt->bindValue(':nombre', $nombre);
        $stmt->execute();
        $rol = $stmt->fetch();

        return $rol !== false ? $rol : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        $stmt = self::db()->query('SELECT * FROM roles ORDER BY id');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
