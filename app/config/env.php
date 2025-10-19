<?php
declare(strict_types=1);

namespace App\Config;

use RuntimeException;

final class Env
{
    private static bool $loaded = false;

    /**
     * Load environment variables from a .env file located at the project root.
     */
    public static function load(string $rootPath): void
    {
        if (self::$loaded) {
            return;
        }

        $envFile = $rootPath . '/.env';
        if (!is_readable($envFile)) {
            // Fallback to .env.example for local development when .env is missing
            $envFile = $rootPath . '/.env.example';
            if (!is_readable($envFile)) {
                throw new RuntimeException('Environment file not found.');
            }
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new RuntimeException('Unable to read environment file.');
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $key = trim($key);
            $value = trim($value);
            if ($value !== '' && ($value[0] === '"' || $value[0] === '\'')) {
                $value = trim($value, "\"' ");
            }

            if ($key !== '') {
                putenv($key . '=' . $value);
                $_ENV[$key] = $value;
                if (!array_key_exists($key, $_SERVER)) {
                    $_SERVER[$key] = $value;
                }
            }
        }

        self::$loaded = true;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
    }
}
