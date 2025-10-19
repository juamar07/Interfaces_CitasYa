<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class TipoComentario extends BaseModel
{
    public static function idPorNombre(string $nombre): ?int
    {
        $stmt = self::db()->prepare('SELECT id FROM tipo_comentario WHERE nombre = :nombre LIMIT 1');
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
        $stmt = self::db()->query('SELECT * FROM tipo_comentario ORDER BY id');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
