<?php
declare(strict_types=1);

namespace App\Core;

final class Validator
{
    /**
     * Validate registration fields for a user account.
     *
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    public static function validateClienteRegistro(array $data): array
    {
        $errors = [];

        $email = filter_var((string)($data['correo'] ?? ''), FILTER_VALIDATE_EMAIL);
        if ($email === false) {
            $errors['correo'] = 'Correo electrónico inválido.';
        }

        $telefono = preg_replace('/\D+/', '', (string)($data['telefono'] ?? ''));
        if ($telefono === null || strlen($telefono) < 7 || strlen($telefono) > 15) {
            $errors['telefono'] = 'El teléfono debe tener entre 7 y 15 dígitos.';
        }

        $usuario = (string)($data['usuario'] ?? '');
        if ($usuario === '' || !preg_match('/^[a-zA-Z0-9._-]{3,20}$/', $usuario)) {
            $errors['usuario'] = 'El usuario debe tener entre 3 y 20 caracteres alfanuméricos.';
        }

        $password = (string)($data['password'] ?? '');
        if (strlen($password) < 6) {
            $errors['password'] = 'La contraseña debe tener al menos 6 caracteres.';
        }

        $nombre = trim((string)($data['nombre_completo'] ?? ''));
        if ($nombre === '') {
            $errors['nombre_completo'] = 'El nombre es obligatorio.';
        }

        return $errors;
    }
}
