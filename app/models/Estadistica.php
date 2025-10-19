<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Estadistica extends BaseModel
{
    public static function crear(array $data): int
    {
        $sql = 'INSERT INTO estadisticas (
                    alcance,
                    audiencia,
                    negocio_id,
                    periodo_inicio,
                    periodo_fin,
                    negocios_activos,
                    barberos_activos,
                    citas_programadas,
                    citas_canceladas,
                    prom_citas_7d,
                    tasa_cancelacion,
                    tokens_comprados,
                    tokens_consumidos,
                    tokens_saldo_total,
                    suscrip_activas,
                    suscrip_nuevas,
                    comentarios_total,
                    calif_promedio,
                    calif_5,
                    calif_4,
                    calif_3,
                    calif_2,
                    calif_1,
                    recomiendan_si,
                    recomiendan_no,
                    observaciones
                ) VALUES (
                    :alcance,
                    :audiencia,
                    :negocio,
                    :inicio,
                    :fin,
                    :negocios_activos,
                    :barberos_activos,
                    :citas_programadas,
                    :citas_canceladas,
                    :prom_citas_7d,
                    :tasa_cancelacion,
                    :tokens_comprados,
                    :tokens_consumidos,
                    :tokens_saldo_total,
                    :suscrip_activas,
                    :suscrip_nuevas,
                    :comentarios_total,
                    :calif_promedio,
                    :calif_5,
                    :calif_4,
                    :calif_3,
                    :calif_2,
                    :calif_1,
                    :recomiendan_si,
                    :recomiendan_no,
                    :observaciones
                )';

        $stmt = self::db()->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
        return (int) self::db()->lastInsertId();
    }

    public static function ultimo(): ?array
    {
        $stmt = self::db()->query('SELECT * FROM estadisticas ORDER BY creado_en DESC LIMIT 1');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }
}
