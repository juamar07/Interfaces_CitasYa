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

      --c-primary:#5c6bc0;
      --c-primary-d:#3f51b5;
      --c-green:#66BB6A;
      --c-green-d:#57A05D;
    }
    body {
      font-family: 'Open Sans', sans-serif;
      background-color: #eeeeee;
      margin: 0;
      padding: 20px;
      color: #333;
      padding-top: calc(var(--banner-h) + 8px);
    }
    .container {
      max-width: var(--container-w);
      margin: auto;
      padding: var(--container-pad);
      background-color: #ffffff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
      border-radius: 10px;
      border-left: var(--container-bl) solid var(--c-primary);
    }
    h1 { text-align: center; color: #000; font-size: 24px; font-weight: 600; margin-bottom: 20px; }
    label { display: block; margin: 10px 0; color: #003366; font-size: 16px; font-weight: 400; }
    input[type="text"], input[type="email"], input[type="tel"], input[type="password"]{
      width: 100%; padding: 12px; margin-bottom: 20px; border: 2px solid #dddddd; border-radius: 5px;
      box-sizing: border-box; transition: border-color 0.3s; font-size: 16px;
    }
    input:focus{ outline: none; border-color: #7da2a9; }
    button{
      display:block; width:100%; margin:10px 0; border:none; padding:15px 20px; border-radius:5px;
      cursor:pointer; font-weight:600; transition: background-color .3s, transform .3s; color:#fff; font-size:16px;
    }
    .btn-registrar{ background:var(--c-green); }
    .btn-registrar:hover{ background:var(--c-green-d); transform: translateY(-2px); }
    .btn-slim{ width:min(640px,75%); margin-left:auto; margin-right:auto; }
    .business-cta{ text-align:center; margin-top:6px; margin-bottom:4px; }
    .business-cta a{
      display:inline-block; margin-top:6px; padding:10px 14px; border:2px solid var(--c-primary); border-radius:6px;
      color:var(--c-primary); font-weight:700; text-decoration:none; transition: background-color .2s, color .2s;
    }
    .business-cta a:hover{ background:var(--c-primary); color:#fff; }
    p{ text-align:center; font-size:16px; color: #003366; font-weight:400; }
    p a{ color:var(--c-primary); text-decoration:none; } p a:hover{ text-decoration:underline; }

    .app-banner{
      position:fixed; top:0; left:0; right:0; height:var(--banner-h); z-index:9999; background:transparent;
    }
    .app-banner .banner-box{
      height:100%;
      width:min(calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl)),
                calc(100% - var(--page-sidepad)*2));
      margin:0 auto; background:var(--banner-bg); border-bottom:1px solid rgba(0,0,0,.06);
      border-radius:10px; transition: background-color .2s; display:flex; align-items:center;
    }
    .app-banner .banner-box:hover, .app-banner .banner-box:focus-within{ background:var(--banner-bg-hover); }
    .app-banner .banner-inner{ display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:8px; width:100%; padding:0 12px; }
    .banner-back{ justify-self:start; }
    .banner-title{ justify-self:center; font-weight:700; color:#233247; }
    .banner-logo{ justify-self:end; display:inline-flex; align-items:center; }
    .banner-logo img{ width:52px; height:auto; display:block; }
    .back-button{
      display:inline-block; text-decoration:none; font-size:14px; color:var(--c-primary); padding:8px 12px; border:1px solid var(--c-primary); border-radius:6px; transition: background-color .2s, color .2s;
    }
    .back-button:hover{ background:var(--c-primary); color:#fff; }
    .legal-outside{
      margin:18px auto 24px; padding:10px 12px;
      max-width: calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl));
      text-align:center; color:#666; font-size:14px; line-height:1.35;
    }
    .hint{ color:#666; margin-top:-12px; margin-bottom:16px; font-size:13px; }
    .error{ color:#b00020; font-size:13px; margin-top:-12px; margin-bottom:16px; display:none; }
    @media (max-width: 768px){
      .container{ width:100%; padding:10px; }
      h1, .banner-title{ font-size:20px; }
      label, button, p{ font-size:14px; }
      .btn-slim{ width:100%; }
    }
  </style>
<?php
$styles = ob_get_clean();
renderHeader('Registro de Cliente', $styles);
renderBanner('Registro de Cliente');
?>

<div class="container">
  <h1>Registro de Cliente</h1>

  <form id="registroForm" method="post" action="/registro-cliente" novalidate>
      <input type="hidden" name="csrf_token" value="<?= Helpers::e(CSRF::token()) ?>">

      <label for="nombre">Nombre Completo</label>
      <input type="text" id="nombre" name="nombre_completo" autocomplete="name" required>
      <div id="errNombre" class="error">Ingresa tu nombre (mínimo 2 caracteres).</div>

      <label for="correo">Correo</label>
      <input type="email" id="correo" name="correo" autocomplete="email" required>
      <div class="hint">Se utilizará para confirmar tus citas.</div>
      <div id="errCorreo" class="error">Ingresa un correo válido.</div>

      <label for="telefono">Teléfono</label>
      <input type="tel" id="telefono" name="telefono" autocomplete="tel" required>
      <div class="hint">Usa solo números (7 a 15 dígitos).</div>
      <div id="errTelefono" class="error">Ingresa un teléfono válido.</div>

      <label for="usuario">Usuario</label>
      <input type="text" id="usuario" name="usuario" autocomplete="username" required>
      <div class="hint">Puede incluir letras, números y . _ - (3 a 20 caracteres).</div>
      <div id="errUsuario" class="error">Ingresa un usuario válido.</div>

      <label for="contrasena">Contraseña</label>
      <input type="password" id="contrasena" name="password" autocomplete="new-password" required>
      <div class="hint">Mínimo 6 caracteres.</div>
      <div id="errContrasena" class="error">Ingresa una contraseña válida.</div>

      <button type="submit" class="btn-registrar btn-slim">Registrarme</button>
  </form>

  <div class="business-cta">
    ¿Tienes un negocio? <a href="/registrar-negocio">Registra tu barbería</a>
  </div>

  <p>
    ¿Ya tienes cuenta? <a href="/login">Inicia sesión</a>
  </p>
</div>

<?php renderLegal(); ?>

<script>
  document.getElementById('registroForm').addEventListener('submit', function (event) {
    const nombre = document.getElementById('nombre');
    const correo = document.getElementById('correo');
    const telefono = document.getElementById('telefono');
    const usuario = document.getElementById('usuario');
    const contrasena = document.getElementById('contrasena');

    const showError = (id, show) => {
      const el = document.getElementById(id);
      if (el) {
        el.style.display = show ? 'block' : 'none';
      }
    };

    let valid = true;

    if ((nombre.value || '').trim().length < 2) {
      showError('errNombre', true);
      valid = false;
    } else {
      showError('errNombre', false);
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test((correo.value || '').trim())) {
      showError('errCorreo', true);
      valid = false;
    } else {
      showError('errCorreo', false);
    }

    const telefonoValue = (telefono.value || '').replace(/\D+/g, '');
    if (telefonoValue.length < 7 || telefonoValue.length > 15) {
      showError('errTelefono', true);
      valid = false;
    } else {
      showError('errTelefono', false);
    }

    if (!/^[a-zA-Z0-9._-]{3,20}$/.test((usuario.value || '').trim())) {
      showError('errUsuario', true);
      valid = false;
    } else {
      showError('errUsuario', false);
    }

    if ((contrasena.value || '').length < 6) {
      showError('errContrasena', true);
      valid = false;
    } else {
      showError('errContrasena', false);
    }

    if (!valid) {
      event.preventDefault();
    }
  });
</script>

<?php renderFooter(); ?>
