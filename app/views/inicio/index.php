<?php
declare(strict_types=1);

use App\Core\CSRF;

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/banner.php';
require __DIR__ . '/../../includes/legal.php';
require __DIR__ . '/../../includes/footer.php';

ob_start();
?>
<style>
    /* ===== Variables de layout (coinciden con tu card) ===== */
    :root{
      --container-w: 800px;          /* ancho de contenido del card (.container) */
      --container-pad: 20px;         /* padding lateral del card */
      --container-bl: 4px;           /* border-left del card */
      --banner-h: 64px;
      --page-sidepad: 16px;          /* respiración a los lados en móviles */
      --banner-bg: #e6e9ee;          /* gris nardo */
      --banner-bg-hover: #d7dbe3;    /* hover/focus */
    }

    /* ===== Base ===== */
    body {
      font-family: 'Open Sans', sans-serif;
      background-color: #eeeeee;
      margin: 0;
      padding: 20px;
      color: #333;
      /* deja espacio para el banner fijo */
      padding-top: calc(var(--banner-h) + 8px);
    }
    .container {
      max-width: var(--container-w);
      margin: auto;
      padding: var(--container-pad);
      background-color: #ffffff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
      border-radius: 10px;
      border-left: var(--container-bl) solid #5c6bc0;
    }

    h1 { text-align: center; color: #000; font-size: 24px; font-weight: 600; margin-bottom: 20px; }

    /* ===== Botones ===== */
    button {
      display: block; width: 100%; margin: 10px 0;
      border: none; padding: 15px 20px; border-radius: 5px;
      cursor: pointer; font-weight: 600; transition: background-color 0.3s, transform 0.3s;
      color: white; font-size: 16px;
    }
    /* colores */
    .btn-inicio { background-color: #5c6bc0; }
    .btn-inicio:hover { background-color: #3f51b5; transform: translateY(-2px); }
    .btn-registrar { background-color: #66BB6A; }
    .btn-registrar:hover { background-color: #57A05D; transform: translateY(-2px); }

    /* NUEVO: tamaños (≈ 75% del ancho del card) */
    .btn-slim {
      width: min(640px, 75%);
      margin-left: auto;
      margin-right: auto;
    }
    .btn-row-2{
      width: min(640px, 75%);
      margin: 10px auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }
    @media (max-width: 560px){
      .btn-slim{ width: 100%; }
      .btn-row-2{ width: 100%; grid-template-columns: 1fr; }
    }

    footer { text-align: center; font-size: 14px; color: #555; margin-top: 20px; }

    /* ===== BANNER SUPERIOR (mismo ancho que el card) ===== */
    .app-banner{
      position: fixed; top: 0; left: 0; right: 0; height: var(--banner-h);
      z-index: 9999; background: transparent; /* el fondo real lo pone .banner-box */
    }
    .app-banner .banner-box{
      height: 100%;
      width: min(calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl)),
                 calc(100% - var(--page-sidepad)*2));
      margin: 0 auto;
      background: var(--banner-bg);
      border-bottom: 1px solid rgba(0,0,0,.06);
      border-radius: 10px;
      transition: background-color .2s ease;
      display: flex; align-items: center;
    }
    .app-banner .banner-box:hover,
    .app-banner .banner-box:focus-within{ background: var(--banner-bg-hover); }

    .app-banner .banner-inner{
      display: grid; grid-template-columns: 1fr auto 1fr; align-items: center;
      gap: 8px; width: 100%; padding: 0 12px;
    }
    .banner-back{ justify-self: start; }
    .banner-title{ justify-self: center; font-weight: 700; color: #233247; }
    .banner-logo{ justify-self: end; display: inline-flex; align-items: center; }
    .banner-logo img{ width: 52px; height: auto; display: block; }

    /* botón volver */
    .back-button{
      display:inline-block; text-decoration:none; font-size:14px; color:#5c6bc0;
      padding:8px 12px; border:1px solid #5c6bc0; border-radius:6px;
      transition: background-color .2s, color .2s;
    }
    .back-button:hover{ background:#5c6bc0; color:#fff; }

    /* ===== LEGAL FUERA DE LA PÁGINA ===== */
    .legal-outside{
      margin: 18px auto 24px;
      padding: 10px 12px;
      max-width: calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl));
      text-align: center; color: #666; font-size: 14px; line-height: 1.35;
    }

    /* ===== DEV SHORTCUTS (TEMPORAL) ===== */
    .dev-shortcuts {
      margin-top: 24px; padding: 12px; border: 1px dashed #999;
      border-radius: 8px; background: #fafafa;
    }
    .dev-shortcuts h2 { font-size: 14px; margin: 0 0 10px; color: #666; }
    .btn-dev { background-color: #009688; }
    .btn-dev:hover { background-color: #00796B; }
    .btn-dev-alt { background-color: #8D6E63; }
    .btn-dev-alt:hover { background-color: #6D4C41; }
    /* ===== FIN DEV SHORTCUTS ===== */

    @media (max-width: 768px) {
      .container { width: 100%; padding: 10px; }
      h1, .banner-title { font-size: 20px; }
      button { font-size: 14px; }
    }
  </style>
<?php
$styles = ob_get_clean();
renderHeader('Bienvenido a Citas Ya', $styles);
renderBanner('Bienvenido a Citas Ya');
?>

<div class="container">
  <section>
    <button id="btn_login" class="btn-inicio btn-slim">Iniciar Sesión</button>
    <button id="btn_registrar_cliente" class="btn-registrar btn-slim">Registrarse</button>
  </section>

  <section class="dev-shortcuts" aria-label="Accesos temporales de desarrollo">
    <h2>Accesos rápidos (temporal)</h2>
    <button id="dev_ir_agendar" class="btn-dev">Ir a: Agendar una cita (Cliente)</button>
    <button id="dev_ir_organizar" class="btn-dev">Ir a: Organizar agenda (Barbero)</button>
    <button id="dev_ir_admin" class="btn-dev-alt">Ir a: Administrador</button>
  </section>

  <footer>
    <p>Reserva servicios y gestiona tu agenda en minutos.</p>
  </footer>
</div>

<?php renderLegal(); ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const go = (id, path) => {
      const el = document.getElementById(id);
      if (el) {
        el.addEventListener('click', function(){ window.location.href = path; });
      }
    };

    go('btn_login', '/login');
    go('btn_registrar_cliente', '/registro-cliente');
    go('dev_ir_agendar', '/agendar');
    go('dev_ir_organizar', '/organizar-agenda');
    go('dev_ir_admin', '/admin');
  });
</script>

<?php renderFooter(); ?>
