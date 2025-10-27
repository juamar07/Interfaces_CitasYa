<?php
declare(strict_types=1);

namespace App\Core;

use App\Models\Usuario;
use RuntimeException;

final class Auth
{
    private const SESSION_USER_KEY = 'auth_user_id';

    private function __construct()
    {
    }

    public static function user(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return null;
        }

        $userId = $_SESSION[self::SESSION_USER_KEY] ?? null;
        if ($userId === null) {
            return null;
        }

        return Usuario::findById((int) $userId);
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function attempt(string $credential, string $password): bool
    {
        $user = Usuario::findByCredential($credential);
        if ($user === null) {
            return false;
        }

        if (!password_verify($password, $user['hash_contrasena'])) {
            return false;
        }

        if (!(bool) $user['activo']) {
            return false;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException('Session has not been started.');
        }

        session_regenerate_id(true);
        $_SESSION[self::SESSION_USER_KEY] = (int) $user['id'];
        if (isset($user['usuario'])) {
            $_SESSION['usuario'] = $user['usuario'];
        } else {
            $_SESSION['usuario'] = (string) ($user['correo'] ?? $user['telefono'] ?? '');
        }
        CSRF::regenerate();

        return true;
    }

    public static function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }

    public static function requireRole(array $roles): void
    {
        $user = self::user();
        if ($user === null || !in_array($user['rol_nombre'], $roles, true)) {
            Helpers::redirect('/login');
        }
    }
}
