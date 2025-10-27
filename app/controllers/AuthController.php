<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Config\Env;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Helpers;
use App\Core\Validator;
use App\Models\Rol;
use App\Models\PasswordReset;
use App\Models\Usuario;
use DateTimeImmutable;
use Exception;
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
            'hash_contrasena' => password_hash($data['password'], PASSWORD_DEFAULT),
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

    public function showForgotForm(): void
    {
        $this->view('password/forgot');
    }

    public function handleForgot(): void
    {
        try {
            CSRF::validate($_POST['csrf_token'] ?? null);
        } catch (RuntimeException $e) {
            Flash::add('error', 'Sesión inválida, recarga la página.');
            $this->redirect('/password/forgot');
        }

        $credential = Helpers::sanitizeString($_POST['credential'] ?? '');
        if ($credential === '') {
            Flash::add('error', 'Ingresa el correo, usuario o teléfono asociado a tu cuenta.');
            $this->redirect('/password/forgot');
        }

        $user = Usuario::findByCredential($credential);

        if ($user !== null) {
            PasswordReset::invalidateForUser((int) $user['id']);
            try {
                $selector = bin2hex(random_bytes(8));
                $token = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                Flash::add('error', 'No fue posible generar el enlace, intenta nuevamente.');
                $this->redirect('/password/forgot');
            }

            $tokenHash = hash('sha256', $token, true);
            $expiresAt = new DateTimeImmutable('+30 minutes');

            PasswordReset::create((int) $user['id'], $selector, $tokenHash, $expiresAt);

            $appUrl = rtrim(Env::get('APP_URL', ''), '/');
            $resetPath = sprintf('/password/reset?selector=%s&token=%s', $selector, $token);
            if ($appUrl !== '') {
                $resetLink = $appUrl . $resetPath;
            } else {
                $resetLink = $resetPath;
            }

            if (Env::get('APP_ENV', 'local') === 'local' || Env::get('APP_PREVIEW', '0') === '1') {
                Flash::add('info', 'Enlace de restablecimiento: ' . $resetLink);
            }
        }

        Flash::add('success', 'Si la cuenta existe, te enviaremos instrucciones de restablecimiento.');
        $this->redirect('/password/forgot');
    }

    public function showResetForm(): void
    {
        $selector = Helpers::sanitizeString($_GET['selector'] ?? '');
        $token = Helpers::sanitizeString($_GET['token'] ?? '');

        if ($selector === '' || $token === '' || !ctype_xdigit($selector) || !ctype_xdigit($token)) {
            Flash::add('error', 'El enlace de restablecimiento no es válido.');
            $this->redirect('/password/forgot');
        }

        $reset = PasswordReset::findActiveBySelector($selector);
        if ($reset === null) {
            Flash::add('error', 'El enlace ha expirado o ya fue utilizado.');
            $this->redirect('/password/forgot');
        }

        $expected = hash('sha256', $token, true);
        if (!hash_equals($reset['token_hash'], $expected)) {
            Flash::add('error', 'El enlace de restablecimiento no es válido.');
            $this->redirect('/password/forgot');
        }

        $this->view('password/reset', [
            'selector' => $selector,
            'token' => $token,
        ]);
    }

    public function handleReset(): void
    {
        try {
            CSRF::validate($_POST['csrf_token'] ?? null);
        } catch (RuntimeException $e) {
            Flash::add('error', 'Sesión inválida, recarga la página.');
            $this->redirect('/password/reset');
        }

        $selector = Helpers::sanitizeString($_POST['selector'] ?? '');
        $token = Helpers::sanitizeString($_POST['token'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['password_confirm'] ?? '');

        if ($selector === '' || $token === '' || !ctype_xdigit($selector) || !ctype_xdigit($token)) {
            Flash::add('error', 'El enlace de restablecimiento no es válido.');
            $this->redirect('/password/forgot');
        }

        if (strlen($password) < 6) {
            Flash::add('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
            $this->redirect(sprintf('/password/reset?selector=%s&token=%s', $selector, $token));
        }

        if (!hash_equals($password, $confirm)) {
            Flash::add('error', 'Las contraseñas no coinciden.');
            $this->redirect(sprintf('/password/reset?selector=%s&token=%s', $selector, $token));
        }

        $reset = PasswordReset::findActiveBySelector($selector);
        if ($reset === null) {
            Flash::add('error', 'El enlace ha expirado o ya fue utilizado.');
            $this->redirect('/password/forgot');
        }

        $expected = hash('sha256', $token, true);
        if (!hash_equals($reset['token_hash'], $expected)) {
            Flash::add('error', 'El enlace de restablecimiento no es válido.');
            $this->redirect('/password/forgot');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        Usuario::updatePassword((int) $reset['usuario_id'], $hash);
        PasswordReset::markUsed((int) $reset['id']);

        Flash::add('success', 'Tu contraseña fue actualizada. Ahora puedes iniciar sesión.');
        $this->redirect('/login');
    }
}
