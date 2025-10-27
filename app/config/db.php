<?php
declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $dbName = Env::get('DB_NAME', 'citasya');
        $user = Env::get('DB_USER', 'root');
        $password = Env::get('DB_PASS', '');
        $host = Env::get('DB_HOST', 'localhost');
        $port = Env::get('DB_PORT', '3306');
        $socket = trim(Env::get('DB_SOCKET', ''));

        $dsnCandidates = [];
        if ($socket !== '') {
            $dsnCandidates[] = sprintf('mysql:unix_socket=%s;dbname=%s;charset=utf8mb4', $socket, $dbName);
        }

        $hosts = [$host];
        if ($host === 'localhost') {
            $hosts[] = '127.0.0.1';
            $hosts[] = '::1';
        }

        foreach (array_unique($hosts) as $candidateHost) {
            $dsnCandidates[] = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $candidateHost, $port, $dbName);
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $exceptions = [];
        foreach ($dsnCandidates as $dsn) {
            try {
                self::$connection = new PDO($dsn, $user, $password, $options);
                return self::$connection;
            } catch (PDOException $e) {
                $exceptions[] = $e->getMessage();
            }
        }

        throw new RuntimeException('Database connection failed: ' . implode(' | ', $exceptions));
    }
}
