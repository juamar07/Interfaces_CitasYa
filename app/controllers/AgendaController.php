<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\Cita;
use RuntimeException;

final class AgendaController extends Controller
{
    public function guardarPatron(): void
    {
        Auth::requireRole(['barbero', 'dueno_negocio']);

        try {
            CSRF::validate($_POST['csrf_token'] ?? null);
        } catch (RuntimeException $e) {
            Flash::add('error', 'Sesión inválida, recarga la página.');
            $this->redirect('/organizar-agenda');
        }

        Flash::add('success', 'Patrón de agenda recibido. Implementa la lógica de persistencia.');
        $this->redirect('/organizar-agenda');
    }

    public function ics(): void
    {
        Auth::requireRole(['barbero', 'dueno_negocio']);

        $user = Auth::user();
        if ($user === null) {
            Flash::add('error', 'Debes iniciar sesión.');
            $this->redirect('/login');
        }

        $citas = Cita::listarPorPersonal((int) ($user['personal_id'] ?? 0));

        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="agenda.ics"');

        echo "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//CitasYa//ES\n";
        foreach ($citas as $cita) {
            $uid = 'cita-' . $cita['id'] . '@citasya.local';
            $inicio = gmdate('Ymd\THis\Z', strtotime($cita['inicia_en']));
            $fin = gmdate('Ymd\THis\Z', strtotime($cita['termina_en']));
            $summary = 'Cita ' . ($cita['servicio_nombre'] ?? '') . ' con ' . ($cita['cliente_nombre'] ?? 'cliente');
            echo "BEGIN:VEVENT\nUID:$uid\nDTSTAMP:" . gmdate('Ymd\THis\Z') . "\nDTSTART:$inicio\nDTEND:$fin\nSUMMARY:" . addcslashes($summary, ",;\\") . "\nEND:VEVENT\n";
        }
        echo "END:VCALENDAR";
        exit;
    }
}
