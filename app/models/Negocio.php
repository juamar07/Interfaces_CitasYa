<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;
use RuntimeException;

final class Negocio extends BaseModel
{
    public static function findById(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM negocios WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public static function findByNombre(string $nombre): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM negocios WHERE nombre = :nombre LIMIT 1');
        $stmt->bindValue(':nombre', $nombre);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public static function create(array $data): int
    {
        $sql = 'INSERT INTO negocios (nombre, tokens, direccion, latitud, longitud, activo)
                VALUES (:nombre, :tokens, :direccion, :latitud, :longitud, :activo)';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':nombre', $data['nombre']);
        $stmt->bindValue(':tokens', $data['tokens'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':direccion', $data['direccion']);
        $stmt->bindValue(':latitud', $data['latitud']);
        $stmt->bindValue(':longitud', $data['longitud']);
        $stmt->bindValue(':activo', $data['activo'] ?? false, PDO::PARAM_BOOL);
        $stmt->execute();

        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $sql = 'UPDATE negocios SET nombre = :nombre, direccion = :direccion, latitud = :latitud, longitud = :longitud, activo = :activo WHERE id = :id';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':nombre', $data['nombre']);
        $stmt->bindValue(':direccion', $data['direccion']);
        $stmt->bindValue(':latitud', $data['latitud']);
        $stmt->bindValue(':longitud', $data['longitud']);
        $stmt->bindValue(':activo', $data['activo'] ?? false, PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function findByPropietario(int $usuarioId): ?array
    {
        $sql = 'SELECT n.* FROM negocios n INNER JOIN personal p ON p.negocio_id = n.id WHERE p.usuario_id = :usuario AND p.propietario = 1 LIMIT 1';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':usuario', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public static function ensureOwner(int $negocioId, int $usuarioId): void
    {
        $db = self::db();
        $sql = 'SELECT 1 FROM personal WHERE negocio_id = :negocio AND usuario_id = :usuario AND propietario = 1 LIMIT 1';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':negocio', $negocioId, PDO::PARAM_INT);
        $stmt->bindValue(':usuario', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn() === false) {
            $insert = $db->prepare('INSERT INTO personal (negocio_id, usuario_id, propietario, nombre_publico, activo) VALUES (:negocio, :usuario, 1, :nombre, 1)');
            $insert->bindValue(':negocio', $negocioId, PDO::PARAM_INT);
            $insert->bindValue(':usuario', $usuarioId, PDO::PARAM_INT);
            $insert->bindValue(':nombre', 'Propietario');
            $insert->execute();
        }
    }
}
