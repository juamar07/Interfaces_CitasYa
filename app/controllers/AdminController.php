<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\AdminUsuario;
use App\Models\Estadistica;
use RuntimeException;

final class AdminController extends Controller
{
    public function panel(): void
    {
        Auth::requireRole(['admin']);

        $estadistica = Estadistica::ultimo();
        $this->view('admin/panel', [
            'estadistica' => $estadistica,
        ]);
    }

    public function buildSnapshot(): void
    {
        Auth::requireRole(['admin']);

        try {
            CSRF::validate($_POST['csrf_token'] ?? null);
        } catch (RuntimeException $e) {
            Flash::add('error', 'Sesión inválida, recarga la página.');
            $this->redirect('/admin');
        }

        $payload = [
            'alcance' => 'pagina',
            'audiencia' => 'solo_admin',
            'negocio' => null,
            'inicio' => date('Y-m-d'),
            'fin' => date('Y-m-d'),
            'negocios_activos' => 0,
            'barberos_activos' => 0,
            'citas_programadas' => 0,
            'citas_canceladas' => 0,
            'prom_citas_7d' => 0,
            'tasa_cancelacion' => 0,
            'tokens_comprados' => 0,
            'tokens_consumidos' => 0,
            'tokens_saldo_total' => 0,
            'suscrip_activas' => 0,
            'suscrip_nuevas' => 0,
            'comentarios_total' => 0,
            'calif_promedio' => 0,
            'calif_5' => 0,
            'calif_4' => 0,
            'calif_3' => 0,
            'calif_2' => 0,
            'calif_1' => 0,
            'recomiendan_si' => 0,
            'recomiendan_no' => 0,
            'observaciones' => 'Snapshot generado manualmente.',
        ];

        $estadisticaId = Estadistica::crear($payload);

        $admin = Auth::user();
        if ($admin !== null) {
            $adminRow = AdminUsuario::obtenerPorUsuario((int) $admin['id']);
            if ($adminRow !== null) {
                AdminUsuario::actualizarEstadistica((int) $adminRow['id'], $estadisticaId);
            }
        }

        Flash::add('success', 'Snapshot generado.');
        $this->redirect('/admin');
    }
}
