<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Comentario extends BaseModel
{
    public static function crear(array $data): int
    {
        $sql = 'INSERT INTO comentarios (tipo_comentario_id, negocio_id, usuario_autor_id, nombre_autor, calificacion, recomienda, texto, sentimiento, visible)
                VALUES (:tipo, :negocio, :usuario, :nombre, :calificacion, :recomienda, :texto, :sentimiento, :visible)';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':tipo', $data['tipo_comentario_id'], PDO::PARAM_INT);
        $stmt->bindValue(':negocio', $data['negocio_id']);
        $stmt->bindValue(':usuario', $data['usuario_autor_id']);
        $stmt->bindValue(':nombre', $data['nombre_autor']);
        $stmt->bindValue(':calificacion', $data['calificacion'], PDO::PARAM_INT);
        $stmt->bindValue(':recomienda', $data['recomienda'], PDO::PARAM_BOOL);
        $stmt->bindValue(':texto', $data['texto']);
        $stmt->bindValue(':sentimiento', $data['sentimiento']);
        $stmt->bindValue(':visible', $data['visible'] ?? true, PDO::PARAM_BOOL);
        $stmt->execute();
        return (int) self::db()->lastInsertId();
    }
}
