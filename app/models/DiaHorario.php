<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class DiaHorario extends BaseModel
{
    public static function crear(array $data): int
    {
        $sql = 'INSERT INTO dia_horario (conjunto_horario_id, dia_id, trabaja, hora_inicio, hora_fin, almuerzo_inicio, almuerzo_fin)
                VALUES (:conjunto, :dia, :trabaja, :inicio, :fin, :almuerzo_inicio, :almuerzo_fin)';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':conjunto', $data['conjunto_horario_id'], PDO::PARAM_INT);
        $stmt->bindValue(':dia', $data['dia_id'], PDO::PARAM_INT);
        $stmt->bindValue(':trabaja', $data['trabaja'], PDO::PARAM_BOOL);
        $stmt->bindValue(':inicio', $data['hora_inicio']);
        $stmt->bindValue(':fin', $data['hora_fin']);
        $stmt->bindValue(':almuerzo_inicio', $data['almuerzo_inicio']);
        $stmt->bindValue(':almuerzo_fin', $data['almuerzo_fin']);
        $stmt->execute();
        return (int) self::db()->lastInsertId();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function listarPorConjunto(int $conjuntoId): array
    {
        $stmt = self::db()->prepare('SELECT * FROM dia_horario WHERE conjunto_horario_id = :conjunto ORDER BY dia_id');
        $stmt->bindValue(':conjunto', $conjuntoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
