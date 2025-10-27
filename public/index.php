<?php
declare(strict_types=1);

use App\Config\Env;
use App\Core\Auth;
use App\Core\Router;
use App\Core\View;

require __DIR__ . '/../vendor_autoload.php';

$rootPath = dirname(__DIR__);
Env::load($rootPath);

date_default_timezone_set('UTC');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$previewMode = Env::get('APP_PREVIEW', '0') === '1';
if ($previewMode && ($_GET['preview'] ?? '') === '1') {
    $view = $_GET['v'] ?? '';
    $view = trim(str_replace(['..', '\\'], '', $view), '/');

    $allowedViews = [
        'inicio/inicio',
        'login/login',
        'registro_cliente/registro_cliente',
        'registrar_negocio/registrar_negocio',
        'organizar_agenda/organizar_agenda',
        'mi_agenda/mi_agenda',
        'agendar/agendar',
        'cancelar/cancelar',
        'pagos/pagos',
        'comentarios/comentarios',
        'admin/panel',
        'password/forgot',
        'password/reset',
    ];

    if ($view === '' || !in_array($view, $allowedViews, true)) {
        http_response_code(404);
        echo 'Vista no disponible';
        return;
    }

    View::render($view);
    return;
}

$router = new Router();

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH) ?? '/';
$normalizedPath = $path === '/' ? '/' : '/' . trim($path, '/');

$isAsset = strpos($normalizedPath, '/assets/') === 0 || $normalizedPath === '/favicon.ico';
$publicGetRoutes = ['/', '/login', '/registro-cliente', '/comentarios', '/password/forgot', '/password/reset'];
$publicPostRoutes = ['/login', '/registro-cliente', '/comentarios/nuevo', '/password/forgot', '/password/reset'];

$requiresGuard = !$isAsset;
if ($requiresGuard) {
    if ($requestMethod === 'GET' && in_array($normalizedPath, $publicGetRoutes, true)) {
        $requiresGuard = false;
    }
    if ($requestMethod === 'POST' && in_array($normalizedPath, $publicPostRoutes, true)) {
        $requiresGuard = false;
    }
}

if ($requiresGuard && !($previewMode && ($_GET['preview'] ?? '') === '1')) {
    require __DIR__ . '/../app/includes/session_guard.php';
}

$router->get('/', function (): void {
    View::render('inicio/inicio');
});

$router->get('/login', function (): void {
    View::render('login/login');
});
$router->post('/login', 'AuthController@login');

$router->get('/registro-cliente', function (): void {
    View::render('registro_cliente/registro_cliente');
});
$router->post('/registro-cliente', 'AuthController@registerCliente');

$router->get('/registrar-negocio', function (): void {
    View::render('registrar_negocio/registrar_negocio');
});
$router->post('/registrar-negocio', 'AuthController@registerNegocio');

$router->get('/organizar-agenda', function (): void {
    Auth::requireRole(['barbero', 'dueno_negocio']);
    View::render('organizar_agenda/organizar_agenda');
});
$router->post('/organizar-agenda/guardar', 'AgendaController@guardarPatron');

$router->get('/mi-agenda', function (): void {
    Auth::requireRole(['barbero', 'dueno_negocio']);
    View::render('mi_agenda/mi_agenda');
});
$router->get('/mi-agenda/ics', 'AgendaController@ics');

$router->get('/agendar', function (): void {
    Auth::requireRole(['cliente']);
    View::render('agendar/agendar');
});
$router->post('/agendar', 'CitasController@agendar');

$router->get('/cancelar', function (): void {
    Auth::requireRole(['cliente', 'barbero', 'dueno_negocio']);
    View::render('cancelar/cancelar');
});
$router->post('/cancelar', 'CitasController@cancelar');
$router->post('/reagendar', 'CitasController@reagendar');

$router->get('/pagos', function (): void {
    Auth::requireRole(['dueno_negocio']);
    View::render('pagos/pagos');
});
$router->post('/pagos/redirigir', 'PagosController@redirigirMercadoPago');
$router->post('/pagos/ipn', 'PagosController@ipn');

$router->get('/comentarios', function (): void {
    View::render('comentarios/comentarios');
});
$router->post('/comentarios/nuevo', 'ComentariosController@nuevo');

$router->get('/password/forgot', 'AuthController@showForgotForm');
$router->post('/password/forgot', 'AuthController@handleForgot');
$router->get('/password/reset', 'AuthController@showResetForm');
$router->post('/password/reset', 'AuthController@handleReset');

$router->get('/admin', 'AdminController@panel');
$router->post('/admin/build-snapshot', 'AdminController@buildSnapshot');

$router->dispatch($requestMethod, $requestUri);
