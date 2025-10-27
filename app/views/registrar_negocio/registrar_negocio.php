<?php
declare(strict_types=1);

require __DIR__ . '/../../includes/session_guard.php';
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/banner.php';
require __DIR__ . '/../../includes/legal.php';
require __DIR__ . '/../../includes/footer.php';

ob_start();
?>
<style>
:root{
  --container-w: 920px;
  --container-pad: 20px;
  --container-bl: 4px;

  --banner-h: 64px;
  --page-sidepad: 16px;
  --banner-bg: #e6e9ee;
  --banner-bg-hover: #d7dbe3;
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
  position: relative;
  max-width: var(--container-w);
  margin: auto;
  padding: var(--container-pad);
  background-color: #ffffff;
  box-shadow: 0 4px 8px rgba(0,0,0,0.05);
  border-radius: 10px;
  border-left: var(--container-bl) solid #5c6bc0;
}
h1, h2 {
  text-align: center;
  color: #000;
  font-weight: 600;
  margin: 20px 0;
}
label {
  display: block;
  margin: 10px 0 4px;
  color: #003366;
  font-size: 16px;
}
input[type="text"],
input[type="email"],
input[type="tel"],
input[type="password"],
select {
  width: 100%;
  padding: 10px;
  border: 2px solid #dddddd;
  border-radius: 5px;
  box-sizing: border-box;
  font-size: 16px;
  transition: border-color 0.3s;
}
input:focus, select:focus { outline: none; border-color: #7da2a9; }

.btn{
  display:block;
  border:none;
  cursor:pointer;
  font-weight:600;
  transition: background-color .2s, transform .2s;
  padding:12px 20px;
  border-radius:5px;
  width:100%;
  margin:10px 0;
  font-size:16px;
  color:#fff;
}
.btn:hover{ transform: translateY(-1px); }

.btn-blue{ background:#5c6bc0; }
.btn-blue:hover{ background:#3f51b5; }
.btn-green{ background:#66bb6a; }
.btn-green:hover{ background:#43a047; }
.btn-red{ background:#ef5350; }
.btn-red:hover{ background:#e53935; }

.btn-icon{
  display:inline-block; padding:4px 8px; margin-right:4px;
  border-radius:4px; font-size:14px; width:auto; color:#fff;
}
.btn-icon-blue{ background:#5c6bc0; }
.btn-icon-blue:hover{ background:#3f51b5; }
.btn-icon-red{ background:#ef5350; }
.btn-icon-red:hover{ background:#e53935; }

.btn-slim{
  width: min(640px, 75%);
  margin: 12px auto;
}

#addService.btn, #addStaff.btn{ background:#5c6bc0 !important; }
#addService.btn:hover, #addStaff.btn:hover{ background:#3f51b5 !important; }

table{ width:100%; border-collapse:collapse; margin-bottom:10px; }
th, td{ padding:8px; text-align:left; }
th{ background:#f5f5f5; color:#555; font-weight:600; }
.token-preview{ color:#666; font-size:14px; }
small{ color:#555; }
.error{ display:block; color:#d32f2f; font-size:14px; }
.badge{
  display:inline-block; padding:4px 8px; border-radius:999px;
  font-size:12px; line-height:1; margin-left:8px;
}
.badge-ok{ background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; }
.badge-warn{ background:#fff3e0; color:#ef6c00; border:1px solid #ffe0b2; }

.app-banner{
  position: fixed; top: 0; left: 0; right: 0; height: var(--banner-h);
  z-index: 9999; background: transparent;
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

.back-button{
  display:inline-block; text-decoration:none; font-size:14px; color:#5c6bc0;
  padding:8px 12px; border:1px solid #5c6bc0; border-radius:6px;
  transition: background-color .2s, color .2s;
}
.back-button:hover{ background:#5c6bc0; color:#fff; }

.legal-outside{
  margin: 18px auto 24px;
  padding: 10px 12px;
  max-width: calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl));
  text-align: center; color: #666; font-size: 14px; line-height: 1.35;
}

.section-divider{
  height:1px; background:#e0e0e0; margin:24px 0;
}

.card{ border:1px solid #dce3f0; border-radius:10px; padding:12px; background:#f9fbff; margin-bottom:12px; }
</style>
<?php
$styles = ob_get_clean();
renderHeader('Registrar Negocio', $styles);
renderBanner('Registrar Negocio');
?>
<div class="container">
  <h1>Registrar Negocio</h1>

  <section>
    <h2>Datos del negocio</h2>
    <label for="bizName">Nombre del negocio <span id="bizStatus" class="badge badge-warn">Nuevo</span></label>
    <input type="text" id="bizName" placeholder="Ej: Barbería Central" />

    <label for="bizAddress">Dirección</label>
    <input type="text" id="bizAddress" placeholder="Ej: Calle 45 #67-89" />

    <label for="bizPhone">Teléfono</label>
    <input type="tel" id="bizPhone" placeholder="Ej: 3001234567" />

    <label for="bizTokens">Tokens actuales</label>
    <input type="number" id="bizTokens" placeholder="0" min="0" />
    <small class="token-preview">Tokens disponibles para agendar servicios.</small>
  </section>

  <div class="section-divider"></div>

  <section>
    <h2>Servicios</h2>
    <div id="servicesList" class="card">
      <p class="muted" style="margin:0;">Aún no has agregado servicios.</p>
    </div>
    <button id="addService" class="btn btn-blue">Agregar servicio</button>
  </section>

  <div class="section-divider"></div>

  <section>
    <h2>Personal</h2>
    <div id="staffList" class="card">
      <p class="muted" style="margin:0;">Aún no has agregado personal.</p>
    </div>
    <button id="addStaff" class="btn btn-blue">Agregar integrante</button>
  </section>

  <div class="section-divider"></div>

  <section>
    <h2>Confirmación</h2>
    <div class="btn-slim">
      <button id="btnSave" class="btn btn-green">Guardar negocio</button>
      <button id="btnReset" class="btn btn-red">Limpiar</button>
    </div>
  </section>
</div>

<template id="tplServiceRow">
  <div class="card service-item" data-id="{{id}}">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <strong>{{nombre}}</strong><br>
        <span class="muted">Duración: {{duracion}} min · Precio: ${{precio}} · Tokens: {{tokens}}</span>
      </div>
      <button class="btn-icon btn-icon-red" data-action="remove">Eliminar</button>
    </div>
  </div>
</template>

<template id="tplStaffRow">
  <div class="card staff-item" data-id="{{id}}">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <strong>{{nombre}}</strong><br>
        <span class="muted">Servicios: {{servicios}}</span>
      </div>
      <button class="btn-icon btn-icon-red" data-action="remove">Eliminar</button>
    </div>
  </div>
</template>

<script>
  const services = [];
  const staff = [];

  const servicesList = document.getElementById('servicesList');
  const staffList = document.getElementById('staffList');
  const tplServiceRow = document.getElementById('tplServiceRow').innerHTML;
  const tplStaffRow = document.getElementById('tplStaffRow').innerHTML;
  const bizStatus = document.getElementById('bizStatus');

  function renderServices() {
    if (!services.length) {
      servicesList.innerHTML = '<p class="muted" style="margin:0;">Aún no has agregado servicios.</p>';
      return;
    }
    servicesList.innerHTML = services.map(service => tplServiceRow
      .replace('{{id}}', service.id)
      .replace('{{nombre}}', service.nombre)
      .replace('{{duracion}}', service.duracion)
      .replace('{{precio}}', service.precio.toLocaleString('es-CO'))
      .replace('{{tokens}}', service.tokens)
    ).join('');
  }

  function renderStaff() {
    if (!staff.length) {
      staffList.innerHTML = '<p class="muted" style="margin:0;">Aún no has agregado personal.</p>';
      return;
    }
    staffList.innerHTML = staff.map(member => tplStaffRow
      .replace('{{id}}', member.id)
      .replace('{{nombre}}', member.nombre)
      .replace('{{servicios}}', member.servicios.length ? member.servicios.join(', ') : 'Sin servicios asignados')
    ).join('');
  }

  document.getElementById('addService').addEventListener('click', () => {
    const nombre = prompt('Nombre del servicio');
    if (!nombre) return;
    const duracion = parseInt(prompt('Duración en minutos (20-120)'), 10);
    if (Number.isNaN(duracion)) return;
    const precio = parseInt(prompt('Precio en COP'), 10) || 0;
    const tokens = Math.max(1, Math.ceil(duracion / 30));
    services.push({ id: Date.now(), nombre, duracion, precio, tokens });
    renderServices();
  });

  document.getElementById('addStaff').addEventListener('click', () => {
    const nombre = prompt('Nombre del integrante');
    if (!nombre) return;
    const serviciosAsignados = services.map(s => s.nombre);
    staff.push({ id: Date.now(), nombre, servicios: serviciosAsignados });
    renderStaff();
  });

  servicesList.addEventListener('click', event => {
    const btn = event.target.closest('button[data-action="remove"]');
    if (!btn) return;
    const item = btn.closest('.service-item');
    if (!item) return;
    const id = parseInt(item.dataset.id, 10);
    const idx = services.findIndex(s => s.id === id);
    if (idx >= 0) {
      services.splice(idx, 1);
      renderServices();
    }
  });

  staffList.addEventListener('click', event => {
    const btn = event.target.closest('button[data-action="remove"]');
    if (!btn) return;
    const item = btn.closest('.staff-item');
    if (!item) return;
    const id = parseInt(item.dataset.id, 10);
    const idx = staff.findIndex(s => s.id === id);
    if (idx >= 0) {
      staff.splice(idx, 1);
      renderStaff();
    }
  });

  document.getElementById('btnSave').addEventListener('click', () => {
    if (!document.getElementById('bizName').value.trim()) {
      alert('Ingresa el nombre del negocio.');
      return;
    }
    if (!services.length) {
      alert('Agrega al menos un servicio.');
      return;
    }
    if (!staff.length) {
      alert('Agrega al menos un integrante del personal.');
      return;
    }
    bizStatus.textContent = 'Guardado';
    bizStatus.className = 'badge badge-ok';
    alert('Negocio guardado (modo demo).');
  });

  document.getElementById('btnReset').addEventListener('click', () => {
    document.getElementById('bizName').value = '';
    document.getElementById('bizAddress').value = '';
    document.getElementById('bizPhone').value = '';
    document.getElementById('bizTokens').value = '';
    services.length = 0;
    staff.length = 0;
    renderServices();
    renderStaff();
    bizStatus.textContent = 'Nuevo';
    bizStatus.className = 'badge badge-warn';
  });
</script>
<?php renderLegal(); ?>
<?php renderFooter(); ?>
