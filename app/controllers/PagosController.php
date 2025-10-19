<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Helpers;
use App\Models\Compra;
use App\Models\MovimientoToken;
use RuntimeException;

final class PagosController extends Controller
{
    public function redirigirMercadoPago(): void
    {
        Auth::requireRole(['dueno_negocio']);

        try {
            CSRF::validate($_POST['csrf_token'] ?? null);
        } catch (RuntimeException $e) {
            Flash::add('error', 'Sesión inválida, recarga la página.');
            $this->redirect('/pagos');
        }

        $paquete = Helpers::sanitizeString($_POST['paquete'] ?? '');
        $montoLibre = (float) ($_POST['monto_libre'] ?? 0);
        $negocioId = (int) ($_POST['negocio_id'] ?? 0);

        if ($paquete === 'libre' && $montoLibre <= 0) {
            Flash::add('error', 'Debes indicar un monto válido.');
            $this->redirect('/pagos');
        }

        $mapLinks = [
            '50' => getenv('MERCADO_PAGO_PUBLIC_LINK_50') ?: '#',
            '100' => getenv('MERCADO_PAGO_PUBLIC_LINK_100') ?: '#',
            '150' => getenv('MERCADO_PAGO_PUBLIC_LINK_150') ?: '#',
            '200' => getenv('MERCADO_PAGO_PUBLIC_LINK_200') ?: '#',
            'plus' => getenv('MERCADO_PAGO_PUBLIC_LINK_PLUS') ?: '#',
            'libre' => getenv('MERCADO_PAGO_PUBLIC_LINK_LIBRE') ?: '#',
        ];

        $redirectUrl = $mapLinks[$paquete] ?? $mapLinks['libre'];

        if ($paquete === 'libre') {
            $redirectUrl = str_replace('{monto}', (string) $montoLibre, $redirectUrl);
        }

        if ($negocioId > 0) {
            Compra::crear([
                'negocio_id' => $negocioId,
                'metodo_id' => 1,
                'estado_id' => 1,
                'tokens' => null,
                'monto_cop' => $paquete === 'libre' ? $montoLibre : (int) $paquete,
                'ref_externa' => bin2hex(random_bytes(8)),
            ]);
        }

        Helpers::redirect($redirectUrl);
    }

    public function ipn(): void
    {
        Helpers::json(['status' => 'ok']);
    }
}
