<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use PDO;

final class ConjuntoHorario extends BaseModel
{
    public static function crear(array $data): int
    {
        $sql = 'INSERT INTO conjunto_horario (negocio_id, personal_id, fecha_inicio, fecha_fin, creado_por)
                VALUES (:negocio, :personal, :inicio, :fin, :creado_por)';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':negocio', $data['negocio_id'], PDO::PARAM_INT);
        $stmt->bindValue(':personal', $data['personal_id'], PDO::PARAM_INT);
        $stmt->bindValue(':inicio', $data['fecha_inicio']);
        $stmt->bindValue(':fin', $data['fecha_fin']);
        $stmt->bindValue(':creado_por', $data['creado_por']);
        $stmt->execute();
        return (int) self::db()->lastInsertId();
    }

    public static function vigentePara(int $personalId, DateTimeImmutable $fecha): ?array
    {
        $sql = 'SELECT * FROM conjunto_horario WHERE personal_id = :personal AND fecha_inicio <= :fecha AND fecha_fin >= :fecha ORDER BY fecha_inicio DESC LIMIT 1';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':personal', $personalId, PDO::PARAM_INT);
        $stmt->bindValue(':fecha', $fecha->format('Y-m-d'));
        $stmt->execute();
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function listarPorPersonal(int $personalId): array
    {
        $stmt = self::db()->prepare('SELECT * FROM conjunto_horario WHERE personal_id = :personal ORDER BY fecha_inicio DESC');
        $stmt->bindValue(':personal', $personalId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
