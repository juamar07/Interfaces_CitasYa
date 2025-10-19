<?php
declare(strict_types=1);

use App\Core\CSRF;
use App\Core\Helpers;

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/banner.php';
require __DIR__ . '/../../includes/legal.php';
require __DIR__ . '/../../includes/footer.php';

ob_start();
?>
<style>
    :root{
      --container-w: 800px;
      --container-pad: 20px;
      --container-bl: 4px;
      --banner-h: 64px;
      --page-sidepad: 16px;
      --banner-bg: #e6e9ee;
      --banner-bg-hover: #d7dbe3;
      --btn-blue: #5c6bc0;
      --btn-blue-hover:#3f51b5;
      --btn-green:#66BB6A;
    }
    body{
      font-family:'Open Sans', sans-serif; background:#eee; margin:0; padding:20px; color:#333;
      padding-top: calc(var(--banner-h) + 8px);
    }
    .container{
      max-width:var(--container-w); margin:auto; padding:var(--container-pad); background:#fff;
      box-shadow:0 4px 8px rgba(0,0,0,.05); border-radius:10px; border-left:var(--container-bl) solid var(--btn-blue);
    }
    h1{ text-align:center; color:#000; font-size:24px; font-weight:600; margin-bottom:20px; }
    label{ display:block; margin:10px 0; color:#003366; font-size:16px; font-weight:400; }
    input{
      width:100%; padding:12px; margin-bottom:20px; border:2px solid #ddd; border-radius:5px; box-sizing:border-box; font-size:16px;
      transition:border-color .3s;
    }
    input:focus{ outline:none; border-color:#7da2a9; }
    .hint{ color:#666; margin-top:-12px; margin-bottom:16px; font-size:13px; }
    .error{ color:#b00020; font-size:13px; margin-top:-12px; margin-bottom:16px; display:none; }

    button{
      display:block; width:100%; margin:10px 0; border:none; padding:15px 20px; border-radius:5px;
      cursor:pointer; font-weight:600; transition: background-color .3s, transform .3s; color:#fff; font-size:16px;
    }
    .btn-blue{ background:var(--btn-blue); }
    .btn-blue:hover{ background:var(--btn-blue-hover); transform: translateY(-2px); }
    .btn-slim{ width:min(640px,75%); margin-left:auto; margin-right:auto; }

    .app-banner{
      position:fixed; top:0; left:0; right:0; height:var(--banner-h); z-index:9999; background:transparent;
    }
    .app-banner .banner-box{
      height:100%;
      width:min(calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl)),
                calc(100% - var(--page-sidepad)*2));
      margin:0 auto; background:var(--banner-bg); border-bottom:1px solid rgba(0,0,0,.06); border-radius:10px;
      transition: background-color .2s; display:flex; align-items:center;
    }
    .app-banner .banner-box:hover, .app-banner .banner-box:focus-within{ background:var(--banner-bg-hover); }
    .app-banner .banner-inner{ display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:8px; width:100%; padding:0 12px; }
    .banner-back{ justify-self:start; }
    .banner-title{ justify-self:center; font-weight:700; color:#233247; }
    .banner-logo{ justify-self:end; display:inline-flex; align-items:center; }
    .banner-logo img{ width:52px; height:auto; display:block; }
    .back-button{
      display:inline-block; text-decoration:none; font-size:14px; color:var(--btn-blue);
      padding:8px 12px; border:1px solid var(--btn-blue); border-radius:6px; transition: background-color .2s, color .2s;
    }
    .back-button:hover{ background:var(--btn-blue); color:#fff; }

    .legal-outside{
      margin:18px auto 24px; padding:10px 12px;
      max-width: calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl));
      text-align:center; color:#666; font-size:14px; line-height:1.35;
    }
    @media (max-width:768px){
      .container{ width:100%; padding:10px; }
      h1, .banner-title{ font-size:20px; }
      label, button{ font-size:14px; }
      .btn-slim{ width:100%; }
    }
  </style>
<?php
$styles = ob_get_clean();
renderHeader('Iniciar sesión', $styles);
renderBanner('Iniciar sesión');
?>

<div class="container">
  <h1>Iniciar sesión</h1>

  <form id="loginForm" method="post" action="/login" novalidate>
      <input type="hidden" name="csrf_token" value="<?= Helpers::e(CSRF::token()) ?>">

      <label for="ident">Correo, usuario o número</label>
      <input id="ident" type="text" name="credential" autocomplete="username" required>
      <div class="hint">Puedes usar cualquiera de tus datos registrados.</div>
      <div id="errIdent" class="error">Ingresa un dato válido.</div>

      <label for="pass">Contraseña</label>
      <input id="pass" type="password" name="password" autocomplete="current-password" required>
      <div id="errPass" class="error">Ingresa tu contraseña.</div>

      <button type="submit" class="btn-blue btn-slim">Iniciar Sesión</button>
  </form>

  <p style="text-align:center; color:#003366;">
    ¿No tienes cuenta? <a href="/registro-cliente">Regístrate</a>
  </p>
</div>

<?php renderLegal(); ?>

<script>
  document.getElementById('loginForm').addEventListener('submit', function (event) {
    const ident = document.getElementById('ident');
    const pass = document.getElementById('pass');
    let valid = true;

    if (!(ident.value || '').trim()) {
      document.getElementById('errIdent').style.display = 'block';
      valid = false;
    } else {
      document.getElementById('errIdent').style.display = 'none';
    }

    if (!(pass.value || '').trim()) {
      document.getElementById('errPass').style.display = 'block';
      valid = false;
    } else {
      document.getElementById('errPass').style.display = 'none';
    }

    if (!valid) {
      event.preventDefault();
    }
  });
</script>

<?php renderFooter(); ?>
