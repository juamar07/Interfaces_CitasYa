<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Controller;
use App\Core\Flash;
use RuntimeException;

final class CitasController extends Controller
{
    public function agendar(): void
    {
        Auth::requireRole(['cliente']);

        try {
            CSRF::validate($_POST['csrf_token'] ?? null);
        } catch (RuntimeException $e) {
            Flash::add('error', 'Sesión inválida, recarga la página.');
            $this->redirect('/agendar');
        }

        Flash::add('success', 'Solicitud de agendamiento recibida. Implementa la lógica de reserva.');
        $this->redirect('/agendar');
    }

    public function cancelar(): void
    {
        Auth::requireRole(['cliente', 'barbero', 'dueno_negocio']);

        try {
            CSRF::validate($_POST['csrf_token'] ?? null);
        } catch (RuntimeException $e) {
            Flash::add('error', 'Sesión inválida, recarga la página.');
            $this->redirect('/cancelar');
        }

        Flash::add('success', 'Cancelación registrada. Implementa la lógica de cancelación.');
        $this->redirect('/cancelar');
    }

    public function reagendar(): void
    {
        Auth::requireRole(['cliente', 'barbero', 'dueno_negocio']);

        try {
            CSRF::validate($_POST['csrf_token'] ?? null);
        } catch (RuntimeException $e) {
            Flash::add('error', 'Sesión inválida, recarga la página.');
            $this->redirect('/mi-agenda');
        }

        Flash::add('success', 'Reagendamiento en proceso. Implementa la lógica de reagendar.');
        $this->redirect('/mi-agenda');
    }
}
