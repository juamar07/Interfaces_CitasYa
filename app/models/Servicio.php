<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Servicio extends BaseModel
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function listarPorNegocio(int $negocioId): array
    {
        $stmt = self::db()->prepare('SELECT * FROM servicios WHERE negocio_id = :negocio ORDER BY orden, nombre');
        $stmt->bindValue(':negocio', $negocioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear(array $data): int
    {
        $sql = 'INSERT INTO servicios (negocio_id, nombre, duracion_min, precio_cop, costo_tokens, activo, orden)
                VALUES (:negocio, :nombre, :duracion, :precio, :tokens, :activo, :orden)';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':negocio', $data['negocio_id'], PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $data['nombre']);
        $stmt->bindValue(':duracion', $data['duracion_min'], PDO::PARAM_INT);
        $stmt->bindValue(':precio', $data['precio_cop']);
        $stmt->bindValue(':tokens', $data['costo_tokens'], PDO::PARAM_INT);
        $stmt->bindValue(':activo', $data['activo'] ?? true, PDO::PARAM_BOOL);
        $stmt->bindValue(':orden', $data['orden'] ?? 0, PDO::PARAM_INT);
        $stmt->execute();
        return (int) self::db()->lastInsertId();
    }

    public static function actualizar(int $id, array $data): void
    {
        $sql = 'UPDATE servicios SET nombre = :nombre, duracion_min = :duracion, precio_cop = :precio, costo_tokens = :tokens, activo = :activo, orden = :orden WHERE id = :id';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':nombre', $data['nombre']);
        $stmt->bindValue(':duracion', $data['duracion_min'], PDO::PARAM_INT);
        $stmt->bindValue(':precio', $data['precio_cop']);
        $stmt->bindValue(':tokens', $data['costo_tokens'], PDO::PARAM_INT);
        $stmt->bindValue(':activo', $data['activo'] ?? true, PDO::PARAM_BOOL);
        $stmt->bindValue(':orden', $data['orden'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function eliminar(int $id): void
    {
        $stmt = self::db()->prepare('DELETE FROM servicios WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function obtener(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM servicios WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }
}
