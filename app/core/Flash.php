<?php
declare(strict_types=1);

namespace App\Core;

final class Flash
{
    private const SESSION_KEY = '_flash_messages';

    private function __construct()
    {
    }

    public static function add(string $type, string $message): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $_SESSION[self::SESSION_KEY][$type][] = $message;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function consume(): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return [];
        }

        $messages = $_SESSION[self::SESSION_KEY] ?? [];
        unset($_SESSION[self::SESSION_KEY]);
        return $messages;
    }
}
