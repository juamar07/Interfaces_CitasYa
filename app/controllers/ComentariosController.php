<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\CSRF;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Helpers;
use App\Models\Comentario;
use App\Models\TipoComentario;
use RuntimeException;

final class ComentariosController extends Controller
{
    public function nuevo(): void
    {
        try {
            CSRF::validate($_POST['csrf_token'] ?? null);
        } catch (RuntimeException $e) {
            Flash::add('error', 'Sesión inválida, recarga la página.');
            $this->redirect('/comentarios');
        }

        $tipo = Helpers::sanitizeString($_POST['tipo'] ?? '');
        $negocioId = $_POST['negocio_id'] !== '' ? (int) $_POST['negocio_id'] : null;
        $calificacion = (int) ($_POST['calificacion'] ?? 0);
        $recomienda = isset($_POST['recomienda']);
        $texto = Helpers::sanitizeString($_POST['texto'] ?? '');
        $nombre = Helpers::sanitizeString($_POST['nombre_autor'] ?? '');

        $tipoId = TipoComentario::idPorNombre($tipo);
        if ($tipoId === null) {
            Flash::add('error', 'Tipo de comentario inválido.');
            $this->redirect('/comentarios');
        }

        if ($tipo === 'negocio' && $negocioId === null) {
            Flash::add('error', 'Debes seleccionar un negocio.');
            $this->redirect('/comentarios');
        }

        if ($calificacion < 1 || $calificacion > 5) {
            Flash::add('error', 'La calificación debe estar entre 1 y 5.');
            $this->redirect('/comentarios');
        }

        $sentimiento = 'neutro';
        if ($calificacion >= 4) {
            $sentimiento = 'positivo';
        } elseif ($calificacion <= 2) {
            $sentimiento = 'negativo';
        }

        Comentario::crear([
            'tipo_comentario_id' => $tipoId,
            'negocio_id' => $negocioId,
            'usuario_autor_id' => null,
            'nombre_autor' => $nombre !== '' ? $nombre : 'anonymous',
            'calificacion' => $calificacion,
            'recomienda' => $recomienda,
            'texto' => $texto,
            'sentimiento' => $sentimiento,
            'visible' => true,
        ]);

        Flash::add('success', 'Gracias por tu comentario.');
        $this->redirect('/comentarios');
    }
}
