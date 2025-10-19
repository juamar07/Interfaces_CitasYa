<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class CSRF
{
    private const SESSION_KEY = '_csrf_token';

    private function __construct()
    {
    }

    public static function token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException('Session has not been started.');
        }

        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function validate(?string $token): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException('Session has not been started.');
        }

        $sessionToken = $_SESSION[self::SESSION_KEY] ?? null;
        if (!$sessionToken || !$token || !hash_equals($sessionToken, $token)) {
            throw new RuntimeException('Invalid CSRF token.');
        }
    }

    public static function regenerate(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
    }
}
