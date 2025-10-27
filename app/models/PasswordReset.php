<?php
declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use PDO;

final class PasswordReset extends BaseModel
{
    public static function invalidateForUser(int $usuarioId): void
    {
        $stmt = self::db()->prepare('UPDATE password_resets SET usado = 1 WHERE usuario_id = :uid AND usado = 0');
        $stmt->bindValue(':uid', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function create(int $usuarioId, string $selector, string $tokenHash, DateTimeImmutable $expiresAt): void
    {
        $sql = 'INSERT INTO password_resets (usuario_id, selector, token_hash, expira_en, usado)
                VALUES (:uid, :selector, :token_hash, :expira, 0)';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':uid', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':selector', $selector);
        $stmt->bindValue(':token_hash', $tokenHash, PDO::PARAM_LOB);
        $stmt->bindValue(':expira', $expiresAt->format('Y-m-d H:i:s'));
        $stmt->execute();
    }

    public static function findActiveBySelector(string $selector): ?array
    {
        $sql = 'SELECT * FROM password_resets
                WHERE selector = :selector AND usado = 0 AND expira_en > UTC_TIMESTAMP()
                LIMIT 1';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':selector', $selector);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    public static function markUsed(int $id): void
    {
        $stmt = self::db()->prepare('UPDATE password_resets SET usado = 1 WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
