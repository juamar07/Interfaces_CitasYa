<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use PDO;

final class Cita extends BaseModel
{
    public static function crear(array $data): int
    {
        $sql = 'INSERT INTO citas (negocio_id, personal_id, servicio_id, usuario_cliente_id, nombre_invitado, fecha, inicia_en, termina_en, estado_id, notas)
                VALUES (:negocio, :personal, :servicio, :cliente, :invitado, :fecha, :inicia, :termina, :estado, :notas)';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':negocio', $data['negocio_id'], PDO::PARAM_INT);
        $stmt->bindValue(':personal', $data['personal_id'], PDO::PARAM_INT);
        $stmt->bindValue(':servicio', $data['servicio_id'], PDO::PARAM_INT);
        $stmt->bindValue(':cliente', $data['usuario_cliente_id']);
        $stmt->bindValue(':invitado', $data['nombre_invitado']);
        $stmt->bindValue(':fecha', $data['fecha']);
        $stmt->bindValue(':inicia', $data['inicia_en']);
        $stmt->bindValue(':termina', $data['termina_en']);
        $stmt->bindValue(':estado', $data['estado_id'], PDO::PARAM_INT);
        $stmt->bindValue(':notas', $data['notas']);
        $stmt->execute();
        return (int) self::db()->lastInsertId();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function listarPorPersonalEnRango(int $personalId, DateTimeImmutable $inicio, DateTimeImmutable $fin): array
    {
        $sql = 'SELECT c.*, s.nombre AS servicio_nombre, u.nombre_completo AS cliente_nombre
                FROM citas c
                LEFT JOIN servicios s ON s.id = c.servicio_id
                LEFT JOIN usuarios u ON u.id = c.usuario_cliente_id
                WHERE c.personal_id = :personal AND c.inicia_en BETWEEN :inicio AND :fin
                ORDER BY c.inicia_en';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':personal', $personalId, PDO::PARAM_INT);
        $stmt->bindValue(':inicio', $inicio->format('Y-m-d H:i:s'));
        $stmt->bindValue(':fin', $fin->format('Y-m-d H:i:s'));
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function tieneSolape(int $personalId, string $inicio, string $fin): bool
    {
        $sql = 'SELECT 1 FROM citas WHERE personal_id = :personal AND inicia_en < :fin AND termina_en > :inicio LIMIT 1';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':personal', $personalId, PDO::PARAM_INT);
        $stmt->bindValue(':inicio', $inicio);
        $stmt->bindValue(':fin', $fin);
        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    }

    public static function actualizarEstado(int $citaId, int $estadoId): void
    {
        $stmt = self::db()->prepare('UPDATE citas SET estado_id = :estado WHERE id = :id');
        $stmt->bindValue(':estado', $estadoId, PDO::PARAM_INT);
        $stmt->bindValue(':id', $citaId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function obtener(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM citas WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function listarPorPersonal(int $personalId): array
    {
        $sql = 'SELECT c.*, s.nombre AS servicio_nombre, u.nombre_completo AS cliente_nombre
                FROM citas c
                LEFT JOIN servicios s ON s.id = c.servicio_id
                LEFT JOIN usuarios u ON u.id = c.usuario_cliente_id
                WHERE c.personal_id = :personal AND c.estado_id <> (SELECT id FROM estado_cita WHERE nombre = "cancelada" LIMIT 1)
                ORDER BY c.inicia_en';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':personal', $personalId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
