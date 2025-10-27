<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Helpers;
use App\Core\Validator;
use App\Models\Rol;
use App\Models\Usuario;
use RuntimeException;

final class AuthController extends Controller
{
    public function login(): void
    {
        try {
            CSRF::validate($_POST['csrf_token'] ?? null);
        } catch (RuntimeException $e) {
            Flash::add('error', 'Sesión inválida, recarga la página.');
            $this->redirect('/login');
        }

        $credential = Helpers::sanitizeString($_POST['credential'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($credential === '' || $password === '') {
            Flash::add('error', 'Debes ingresar tus credenciales.');
            $this->redirect('/login');
        }

        if (!Auth::attempt($credential, $password)) {
            Flash::add('error', 'Credenciales inválidas o usuario inactivo.');
            $this->redirect('/login');
        }

        $user = Auth::user();
        if ($user === null) {
            Auth::logout();
            Flash::add('error', 'No fue posible iniciar sesión.');
            $this->redirect('/login');
        }

        $rol = $user['rol_nombre'] ?? '';
        $redirect = match ($rol) {
            'cliente' => '/agendar',
            'barbero' => '/organizar-agenda',
            'dueno_negocio' => '/organizar-agenda',
            'admin' => '/admin',
            default => '/',
        };

        $this->redirect($redirect);
    }

    public function registerCliente(): void
    {
        try {
            CSRF::validate($_POST['csrf_token'] ?? null);
        } catch (RuntimeException $e) {
            Flash::add('error', 'Sesión inválida, recarga la página.');
            $this->redirect('/registro-cliente');
        }

        $data = [
            'nombre_completo' => Helpers::sanitizeString($_POST['nombre_completo'] ?? ''),
            'correo' => strtolower(Helpers::sanitizeString($_POST['correo'] ?? '')),
            'telefono' => preg_replace('/\D+/', '', (string)($_POST['telefono'] ?? '')),
            'usuario' => strtolower(Helpers::sanitizeString($_POST['usuario'] ?? '')),
            'password' => (string)($_POST['password'] ?? ''),
        ];

        $errors = Validator::validateClienteRegistro($data);
        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::add('error', $error);
            }
            $this->redirect('/registro-cliente');
        }

        if (Usuario::emailExists($data['correo'])) {
            Flash::add('error', 'El correo ya está registrado.');
            $this->redirect('/registro-cliente');
        }
        if (Usuario::telefonoExists($data['telefono'])) {
            Flash::add('error', 'El teléfono ya está registrado.');
            $this->redirect('/registro-cliente');
        }
        if (Usuario::usuarioExists($data['usuario'])) {
            Flash::add('error', 'El nombre de usuario ya está en uso.');
            $this->redirect('/registro-cliente');
        }

        $rol = Rol::findByNombre('cliente');
        if ($rol === null) {
            Flash::add('error', 'No se encontró el rol de cliente, ejecuta los seeds.');
            $this->redirect('/registro-cliente');
        }

        $payload = [
            'nombre_completo' => $data['nombre_completo'],
            'correo' => $data['correo'],
            'telefono' => $data['telefono'],
            'usuario' => $data['usuario'],
            'hash_contrasena' => password_hash($data['password'], PASSWORD_BCRYPT),
            'rol_id' => $rol['id'],
            'activo' => true,
        ];

        Usuario::createCliente($payload);
        Flash::add('success', 'Cuenta creada correctamente, inicia sesión.');
        $this->redirect('/login');
    }

    public function registerNegocio(): void
    {
        try {
            CSRF::validate($_POST['csrf_token'] ?? null);
        } catch (RuntimeException $e) {
            Flash::add('error', 'Sesión inválida, recarga la página.');
            $this->redirect('/registrar-negocio');
        }

        Flash::add('success', 'Tu solicitud de registro de negocio ha sido recibida.');
        $this->redirect('/registrar-negocio');
    }
}
