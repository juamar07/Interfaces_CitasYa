<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Personal extends BaseModel
{
    public static function crear(array $data): int
    {
        $sql = 'INSERT INTO personal (negocio_id, usuario_id, propietario, nombre_publico, activo)
                VALUES (:negocio, :usuario, :propietario, :nombre, :activo)';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':negocio', $data['negocio_id'], PDO::PARAM_INT);
        $stmt->bindValue(':usuario', $data['usuario_id']);
        $stmt->bindValue(':propietario', $data['propietario'] ?? false, PDO::PARAM_BOOL);
        $stmt->bindValue(':nombre', $data['nombre_publico']);
        $stmt->bindValue(':activo', $data['activo'] ?? true, PDO::PARAM_BOOL);
        $stmt->execute();
        return (int) self::db()->lastInsertId();
    }

    public static function actualizar(int $id, array $data): void
    {
        $sql = 'UPDATE personal SET nombre_publico = :nombre, activo = :activo WHERE id = :id';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':nombre', $data['nombre_publico']);
        $stmt->bindValue(':activo', $data['activo'] ?? true, PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function asignarServicios(int $personalId, array $servicioIds): void
    {
        $db = self::db();
        $db->prepare('DELETE FROM personal_servicio WHERE personal_id = :personal')->execute([':personal' => $personalId]);
        $insert = $db->prepare('INSERT INTO personal_servicio (personal_id, servicio_id) VALUES (:personal, :servicio)');
        foreach ($servicioIds as $servicioId) {
            $insert->execute([':personal' => $personalId, ':servicio' => $servicioId]);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function listarPorNegocio(int $negocioId): array
    {
        $sql = 'SELECT * FROM personal WHERE negocio_id = :negocio ORDER BY nombre_publico';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':negocio', $negocioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtener(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM personal WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    /**
     * @return array<int>
     */
    public static function serviciosAsignados(int $personalId): array
    {
        $stmt = self::db()->prepare('SELECT servicio_id FROM personal_servicio WHERE personal_id = :personal');
        $stmt->bindValue(':personal', $personalId, PDO::PARAM_INT);
        $stmt->execute();
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public static function buscarPorUsuario(int $usuarioId): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM personal WHERE usuario_id = :usuario LIMIT 1');
        $stmt->bindValue(':usuario', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }
}
