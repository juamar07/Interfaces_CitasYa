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

$router = new Router();

$router->get('/', function (): void {
    View::render('inicio/index');
});

$router->get('/login', function (): void {
    View::render('login/index');
});
$router->post('/login', 'AuthController@login');

$router->get('/registro-cliente', function (): void {
    View::render('registro_cliente/index');
});
$router->post('/registro-cliente', 'AuthController@registerCliente');

$router->get('/registrar-negocio', function (): void {
    View::render('registrar_negocio/index');
});
$router->post('/registrar-negocio', 'AuthController@registerNegocio');

$router->get('/organizar-agenda', function (): void {
    Auth::requireRole(['barbero', 'dueno_negocio']);
    View::render('organizar_agenda/index');
});
$router->post('/organizar-agenda/guardar', 'AgendaController@guardarPatron');

$router->get('/mi-agenda', function (): void {
    Auth::requireRole(['barbero', 'dueno_negocio']);
    View::render('mi_agenda/index');
});
$router->get('/mi-agenda/ics', 'AgendaController@ics');

$router->get('/agendar', function (): void {
    Auth::requireRole(['cliente']);
    View::render('agendar/index');
});
$router->post('/agendar', 'CitasController@agendar');

$router->get('/cancelar', function (): void {
    Auth::requireRole(['cliente', 'barbero', 'dueno_negocio']);
    View::render('cancelar/index');
});
$router->post('/cancelar', 'CitasController@cancelar');
$router->post('/reagendar', 'CitasController@reagendar');

$router->get('/pagos', function (): void {
    Auth::requireRole(['dueno_negocio']);
    View::render('pagos/index');
});
$router->post('/pagos/redirigir', 'PagosController@redirigirMercadoPago');
$router->post('/pagos/ipn', 'PagosController@ipn');

$router->get('/comentarios', function (): void {
    View::render('comentarios/index');
});
$router->post('/comentarios/nuevo', 'ComentariosController@nuevo');

$router->get('/admin', 'AdminController@panel');
$router->post('/admin/build-snapshot', 'AdminController@buildSnapshot');

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
