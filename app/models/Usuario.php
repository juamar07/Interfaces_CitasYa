<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;
use RuntimeException;

final class Usuario extends BaseModel
{
    public static function findById(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT u.*, r.nombre AS rol_nombre FROM usuarios u INNER JOIN roles r ON u.rol_id = r.id WHERE u.id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();

        return $user !== false ? $user : null;
    }

    public static function findByCredential(string $credential): ?array
    {
        $sql = 'SELECT u.*, r.nombre AS rol_nombre
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                WHERE u.correo = :credential OR u.telefono = :credential OR u.usuario = :credential
                LIMIT 1';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':credential', $credential, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        return $user !== false ? $user : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function create(array $data): int
    {
        $sql = 'INSERT INTO usuarios (nombre_completo, correo, telefono, usuario, hash_contrasena, rol_id, activo)
                VALUES (:nombre, :correo, :telefono, :usuario, :hash, :rol_id, :activo)';

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':nombre', $data['nombre_completo']);
        $stmt->bindValue(':correo', $data['correo']);
        $stmt->bindValue(':telefono', $data['telefono']);
        $stmt->bindValue(':usuario', $data['usuario']);
        $stmt->bindValue(':hash', $data['hash_contrasena']);
        $stmt->bindValue(':rol_id', $data['rol_id'], PDO::PARAM_INT);
        $stmt->bindValue(':activo', $data['activo'] ?? true, PDO::PARAM_BOOL);
        $stmt->execute();

        return (int) self::db()->lastInsertId();
    }

    public static function emailExists(string $correo): bool
    {
        $stmt = self::db()->prepare('SELECT 1 FROM usuarios WHERE correo = :correo LIMIT 1');
        $stmt->bindValue(':correo', $correo);
        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    }

    public static function telefonoExists(string $telefono): bool
    {
        $stmt = self::db()->prepare('SELECT 1 FROM usuarios WHERE telefono = :telefono LIMIT 1');
        $stmt->bindValue(':telefono', $telefono);
        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    }

    public static function usuarioExists(string $usuario): bool
    {
        $stmt = self::db()->prepare('SELECT 1 FROM usuarios WHERE usuario = :usuario LIMIT 1');
        $stmt->bindValue(':usuario', $usuario);
        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createCliente(array $data): int
    {
        $db = self::db();
        try {
            $db->beginTransaction();
            $id = self::create($data);
            $db->commit();
            return $id;
        } catch (PDOException $e) {
            $db->rollBack();
            throw new RuntimeException('Error al registrar el cliente: ' . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
