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

  body{
    font-family: 'Open Sans', sans-serif;
    background:#eeeeee;
    margin:0;
    padding:20px;
    color:#333;
    padding-top: calc(var(--banner-h) + 8px);
  }
  .container{
    max-width: var(--container-w);
    margin: 0 auto;
    padding: var(--container-pad);
    background:#fff;
    border-radius:10px;
    box-shadow:0 4px 8px rgba(0,0,0,.05);
    border-left: var(--container-bl) solid #5c6bc0;
  }
  h1{ text-align:center; margin:0 0 14px; font-weight:700; color:#000; }
  h2{ margin:22px 0 12px; font-weight:700; color:#000; text-align:center;}
  h3{ margin:12px 0 8px; color:#233247; }
  label{ display:block; margin:8px 0 4px; color:#003366; }

  select, input[type="text"], input[type="date"], textarea{
    width:100%; padding:10px; border:2px solid #ddd; border-radius:6px; font-size:16px;
    transition: border-color .2s; box-sizing: border-box;
  }
  textarea{ min-height:180px; resize:vertical; }
  select:focus, input:focus, textarea:focus{ outline:none; border-color:#7da2a9; }

  .row{ display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
  .row-3{ display:grid; grid-template-columns: 1fr 1fr 1fr; gap:12px; }
  .align-end{ align-self: end; }
  .muted{ color:#666; font-size:14px; }
  .badge{ display:inline-block; padding:4px 8px; border-radius:999px; font-size:12px; margin-left:8px; }
  .badge-ok{ background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; }
  .badge-warn{ background:#fff3e0; color:#ef6c00; border:1px solid #ffe0b2; }
  .warn{ color:#c62828; font-size:14px; }

  .day-list{ display:flex; flex-wrap:wrap; gap:14px; }
  .day-list label{ display:inline-flex; align-items:center; gap:6px; margin:0; }

  .btn{
    display:block; border:none; border-radius:6px; padding:12px 18px; color:#fff; font-weight:700; cursor:pointer;
    transition: background-color .2s, transform .2s; width:100%; margin:10px 0; font-size:16px;
  }
  .btn:hover{ transform: translateY(-1px); }
  .btn-green{ background:#66bb6a; } .btn-green:hover{ background:#43a047; }
  .btn-blue{ background:#5c6bc0; } .btn-blue:hover{ background:#3f51b5; }
  .btn-red{ background:#ef5350; } .btn-red:hover{ background:#e53935; }
  .btn-slim{ width:min(640px, 75%); margin:12px auto; }
  .btn-icon{ display:inline-block; padding:6px 10px; border-radius:6px; font-size:14px; width:auto; }
  .btn-icon-green{ background:#66bb6a; color:#fff; }
  .btn-icon-green:hover{ background:#43a047; }
  .wide75{ width:75%; margin:12px auto; }
  @media (max-width:768px){ .wide75{ width:100%; } }

  table{ width:100%; border-collapse:collapse; margin:8px 0; }
  th, td{ padding:8px; text-align:left; }
  th{ background:#f5f5f5; color:#555; font-weight:600; }

  .time-picker{ display:flex; gap:8px; align-items:center; }
  .time-picker select{ width:auto; min-width:84px; }

  .app-banner{ position:fixed; top:0; left:0; right:0; height:var(--banner-h); z-index:9999; background:transparent; }
  .app-banner .banner-box{
    height:100%;
    width:min(calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl)), calc(100% - var(--page-sidepad)*2));
    margin:0 auto; background:var(--banner-bg); border-bottom:1px solid rgba(0,0,0,.06); border-radius:10px;
    display:flex; align-items:center; transition:background-color .2s ease;
  }
  .app-banner .banner-box:hover,.app-banner .banner-box:focus-within{ background:var(--banner-bg-hover); }
  .app-banner .banner-inner{ display:grid; grid-template-columns: 1fr auto 1fr; align-items:center; gap:8px; width:100%; padding:0 12px;}
  .banner-back{ justify-self:start; }
  .banner-title{ justify-self:center; font-weight:700; color:#233247; }
  .banner-logo{ justify-self:end; display:inline-flex; align-items:center; }
  .banner-logo img{ width:52px; height:auto; display:block; }
  .back-button{
    display:inline-block; text-decoration:none; font-size:14px; color:#5c6bc0;
    padding:8px 12px; border:1px solid #5c6bc0; border-radius:6px; transition: background-color .2s, color .2s;
  }
  .back-button:hover{ background:#5c6bc0; color:#fff; }

  .legal-outside{
    margin: 18px auto 24px; padding:10px 12px;
    max-width: calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl));
    text-align:center; color:#666; font-size:14px; line-height:1.35;
  }

  @media (max-width: 768px){
    .row, .row-3{ grid-template-columns: 1fr; }
    .btn-slim{ width:100%; }
  }
</style>
<?php
$styles = ob_get_clean();
renderHeader('Organizar agenda', $styles);
renderBanner('Organizar agenda');
?>
<div class="container">
  <h1>Organizar agenda</h1>

  <section>
    <label for="bizName">Nombre de la barbería
      <span id="bizState" class="badge badge-warn">No cargada</span>
    </label>
    <input type="text" id="bizName" list="businessList" placeholder="Ej: Barbería Central">
    <datalist id="businessList"></datalist>
    <small class="muted">Escribe el nombre. La búsqueda ignora mayúsculas/acentos.</small>
  </section>

  <section>
    <label for="staffSelect">Seleccione el personal</label>
    <select id="staffSelect" disabled>
      <option value="">— Seleccione —</option>
    </select>
  </section>

  <section class="wide75" style="margin-top:6px;">
    <button type="button" id="btnMiAgenda" class="btn btn-blue" style="width:100%;">Mi agenda</button>
  </section>

  <section>
    <h2>Configurar rango y horario semanal</h2>
    <div class="row">
      <div>
        <label for="startDate">Fecha inicio</label>
        <input type="date" id="startDate">
      </div>
      <div>
        <label for="endDate">Fecha fin</label>
        <input type="date" id="endDate">
      </div>
    </div>
    <div class="muted">El rango debe tener máximo 90 días. Se puede traslapar con periodos anteriores.</div>
  </section>

  <section>
    <h3>¿Trabaja los mismos horarios todos los días?</h3>
    <div class="day-list">
      <label><input type="radio" name="sameSchedule" value="si" checked> Sí</label>
      <label><input type="radio" name="sameSchedule" value="no"> No, cada día tiene horario distinto</label>
    </div>
  </section>

  <section id="sameScheduleWrap">
    <h3>Horario general</h3>
    <div class="time-picker">
      <div>
        <label for="startHour">Hora inicio</label>
        <select id="startHour"></select>
      </div>
      <div>
        <label for="endHour">Hora fin</label>
        <select id="endHour"></select>
      </div>
      <div>
        <label for="lunchStart">Almuerzo inicio</label>
        <select id="lunchStart"></select>
      </div>
      <div>
        <label for="lunchEnd">Almuerzo fin</label>
        <select id="lunchEnd"></select>
      </div>
    </div>
  </section>

  <section id="perDaySchedule" style="display:none;">
    <h3>Horario por día</h3>
    <div id="daysGrid"></div>
  </section>

  <section>
    <h3>Servicios asignados</h3>
    <div class="muted">Selecciona los servicios que este barbero puede atender.</div>
    <div id="servicesList" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:10px; margin-top:10px;"></div>
    <button type="button" id="btnAddService" class="btn-icon btn-icon-green">Agregar nuevo servicio</button>
  </section>

  <section class="wide75">
    <button id="btnPreview" class="btn btn-blue">Ver vista previa</button>
  </section>

  <section>
    <h2>Resumen del horario</h2>
    <div id="summary" class="muted">Completa la información para ver el resumen.</div>
  </section>

  <section class="btn-slim">
    <button id="btnSave" class="btn btn-green">Guardar horario</button>
    <button id="btnReset" class="btn btn-red">Limpiar</button>
  </section>

  <section>
    <h2>Historial de plantillas</h2>
    <table>
      <thead>
        <tr>
          <th>Rango</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="templatesTable">
        <tr><td colspan="3" class="muted">No hay plantillas guardadas.</td></tr>
      </tbody>
    </table>
  </section>
</div>

<template id="tplDay">
  <div class="card" style="border:1px solid #d0d7e6; padding:12px; border-radius:10px; background:#fafcff;">
    <h4 style="margin:0 0 8px;">{{dia}}</h4>
    <label><input type="checkbox" class="chkWork" data-dia="{{key}}"> Trabaja este día</label>
    <div class="time-picker" style="margin-top:8px;">
      <div>
        <label>Inicio</label>
        <select class="selStart" data-dia="{{key}}"></select>
      </div>
      <div>
        <label>Fin</label>
        <select class="selEnd" data-dia="{{key}}"></select>
      </div>
    </div>
    <div class="time-picker" style="margin-top:8px;">
      <div>
        <label>Almuerzo inicio</label>
        <select class="selLunchStart" data-dia="{{key}}"></select>
      </div>
      <div>
        <label>Almuerzo fin</label>
        <select class="selLunchEnd" data-dia="{{key}}"></select>
      </div>
    </div>
  </div>
</template>

<template id="tplService">
  <label style="display:flex; align-items:center; gap:8px; padding:10px; border:1px solid #dce3f0; border-radius:8px; background:#f9faff;">
    <input type="checkbox" value="{{id}}">
    <span>
      <strong>{{nombre}}</strong><br>
      <span class="muted">Duración: {{duracion}} min · Tokens: {{tokens}}</span>
    </span>
  </label>
</template>

<template id="tplTemplateRow">
  <tr>
    <td>{{rango}}</td>
    <td>{{estado}}</td>
    <td><button class="btn-icon btn-icon-green" data-id="{{id}}">Aplicar</button></td>
  </tr>
</template>

<script>
  const businesses = [
    { id:1, nombre:'Barbería Central', personal:[
      { id:101, nombre:'Carlos Mejía', servicios:[1,2,3] },
      { id:102, nombre:'Juan Pérez', servicios:[1,3] }
    ], servicios:[
      { id:1, nombre:'Corte clásico', duracion:30, tokens:1 },
      { id:2, nombre:'Corte y barba', duracion:50, tokens:2 },
      { id:3, nombre:'Afeitado premium', duracion:40, tokens:2 }
    ] },
    { id:2, nombre:'Caballeros Elite', personal:[
      { id:201, nombre:'Pedro Gómez', servicios:[1,2] },
      { id:202, nombre:'Sergio Ramírez', servicios:[2,3] }
    ], servicios:[
      { id:1, nombre:'Corte clásico', duracion:30, tokens:1 },
      { id:2, nombre:'Corte ejecutivo', duracion:30, tokens:1 },
      { id:3, nombre:'Tratamiento capilar', duracion:60, tokens:2 }
    ] }
  ];

  const templates = [];

  const businessList = document.getElementById('businessList');
  const bizName = document.getElementById('bizName');
  const staffSelect = document.getElementById('staffSelect');
  const servicesList = document.getElementById('servicesList');
  const daysGrid = document.getElementById('daysGrid');
  const tplDay = document.getElementById('tplDay').innerHTML;
  const tplService = document.getElementById('tplService').innerHTML;
  const tplTemplateRow = document.getElementById('tplTemplateRow').innerHTML;
  const templatesTable = document.getElementById('templatesTable');
  const summary = document.getElementById('summary');
  const startHour = document.getElementById('startHour');
  const endHour = document.getElementById('endHour');
  const lunchStart = document.getElementById('lunchStart');
  const lunchEnd = document.getElementById('lunchEnd');
  const sameScheduleWrap = document.getElementById('sameScheduleWrap');
  const perDaySchedule = document.getElementById('perDaySchedule');
  const startDate = document.getElementById('startDate');
  const endDate = document.getElementById('endDate');
  const bizState = document.getElementById('bizState');
  const btnAddService = document.getElementById('btnAddService');

  businessList.innerHTML = businesses.map(b => `<option value="${b.nombre}"></option>`).join('');

  function fillTimeSelect(select) {
    const options = [];
    for (let hour = 5; hour <= 21; hour++) {
      for (let minute = 0; minute < 60; minute += 10) {
        const h = hour.toString().padStart(2, '0');
        const m = minute.toString().padStart(2, '0');
        options.push(`${h}:${m}`);
      }
    }
    select.innerHTML = options.map(o => `<option value="${o}">${o}</option>`).join('');
  }

  [startHour, endHour, lunchStart, lunchEnd].forEach(fillTimeSelect);

  const diasSemana = [
    { key:'lunes', nombre:'Lunes' },
    { key:'martes', nombre:'Martes' },
    { key:'miercoles', nombre:'Miércoles' },
    { key:'jueves', nombre:'Jueves' },
    { key:'viernes', nombre:'Viernes' },
    { key:'sabado', nombre:'Sábado' },
    { key:'domingo', nombre:'Domingo' }
  ];

  daysGrid.innerHTML = diasSemana.map(dia => tplDay.replace(/{{dia}}/g, dia.nombre).replace(/{{key}}/g, dia.key)).join('');

  daysGrid.querySelectorAll('select').forEach(fillTimeSelect);

  let selectedBiz = null;
  let selectedStaff = null;

  bizName.addEventListener('input', () => {
    const normalized = normalize(bizName.value);
    selectedBiz = businesses.find(b => normalize(b.nombre) === normalized) || null;
    staffSelect.innerHTML = '<option value="">— Seleccione —</option>';
    staffSelect.disabled = !selectedBiz;
    servicesList.innerHTML = '';
    summary.textContent = 'Completa la información para ver el resumen.';
    if (!selectedBiz) {
      bizState.textContent = 'No cargada';
      bizState.className = 'badge badge-warn';
      return;
    }
    bizState.textContent = 'Cargada';
    bizState.className = 'badge badge-ok';
    staffSelect.innerHTML += selectedBiz.personal.map(p => `<option value="${p.id}">${p.nombre}</option>`).join('');
  });

  staffSelect.addEventListener('change', () => {
    const staffId = parseInt(staffSelect.value || '0', 10);
    if (!selectedBiz || !staffId) {
      selectedStaff = null;
      servicesList.innerHTML = '';
      return;
    }
    selectedStaff = selectedBiz.personal.find(p => p.id === staffId) || null;
    renderServicios();
    updateSummary();
  });

  function renderServicios() {
    if (!selectedBiz) {
      servicesList.innerHTML = '';
      return;
    }
    servicesList.innerHTML = selectedBiz.servicios.map(serv => {
      const isChecked = selectedStaff && selectedStaff.servicios.includes(serv.id);
      return tplService
        .replace('{{id}}', serv.id)
        .replace('{{nombre}}', serv.nombre)
        .replace('{{duracion}}', serv.duracion)
        .replace('{{tokens}}', serv.tokens)
        .replace('value="', `value="${serv.id}" ${isChecked ? 'checked' : ''} data-duration="${serv.duracion}" data-tokens="${serv.tokens}" `);
    }).join('');
  }

  document.querySelectorAll('input[name="sameSchedule"]').forEach(radio => {
    radio.addEventListener('change', () => {
      const same = document.querySelector('input[name="sameSchedule"]:checked').value === 'si';
      sameScheduleWrap.style.display = same ? 'block' : 'none';
      perDaySchedule.style.display = same ? 'none' : 'block';
    });
  });

  function normalize(value) {
    return value.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase();
  }

  function updateSummary() {
    if (!selectedBiz || !selectedStaff || !startDate.value || !endDate.value) {
      summary.textContent = 'Completa la información para ver el resumen.';
      return;
    }
    const serviciosSeleccionados = Array.from(servicesList.querySelectorAll('input[type="checkbox"]:checked'))
      .map(input => selectedBiz.servicios.find(s => s.id === parseInt(input.value, 10)))
      .filter(Boolean);

    summary.innerHTML = `
      <div style="background:#f5f7fb; border:1px solid #dce3f0; border-radius:10px; padding:16px;">
        <strong>Barbería:</strong> ${selectedBiz.nombre}<br>
        <strong>Personal:</strong> ${selectedStaff ? selectedStaff.nombre : '—'}<br>
        <strong>Rango:</strong> ${startDate.value} al ${endDate.value}<br>
        <strong>Servicios:</strong>
        ${serviciosSeleccionados.length ? serviciosSeleccionados.map(s => s.nombre).join(', ') : 'No has asignado servicios'}
      </div>
    `;
  }

  [startDate, endDate, servicesList].forEach(el => {
    el.addEventListener('change', updateSummary);
    el.addEventListener('input', updateSummary);
  });

  btnAddService.addEventListener('click', () => {
    alert('Aquí se abriría un modal para agregar un servicio.');
  });

  document.getElementById('btnMiAgenda').addEventListener('click', () => {
    window.location.href = '/mi-agenda';
  });

  document.getElementById('btnPreview').addEventListener('click', () => {
    alert('Previsualización del horario en construcción.');
  });

  document.getElementById('btnSave').addEventListener('click', () => {
    if (!selectedBiz || !selectedStaff) {
      alert('Selecciona la barbería y el personal antes de guardar.');
      return;
    }
    if (!startDate.value || !endDate.value) {
      alert('Selecciona el rango de fechas.');
      return;
    }
    templates.unshift({
      id: Date.now(),
      rango: `${startDate.value} al ${endDate.value}`,
      estado: 'Borrador'
    });
    renderTemplates();
    alert('El horario se ha guardado (modo demo).');
  });

  document.getElementById('btnReset').addEventListener('click', () => {
    bizName.value = '';
    staffSelect.innerHTML = '<option value="">— Seleccione —</option>';
    staffSelect.disabled = true;
    servicesList.innerHTML = '';
    startDate.value = '';
    endDate.value = '';
    summary.textContent = 'Completa la información para ver el resumen.';
    selectedBiz = null;
    selectedStaff = null;
    bizState.textContent = 'No cargada';
    bizState.className = 'badge badge-warn';
  });

  function renderTemplates() {
    if (!templates.length) {
      templatesTable.innerHTML = '<tr><td colspan="3" class="muted">No hay plantillas guardadas.</td></tr>';
      return;
    }
    templatesTable.innerHTML = templates.map(tpl => tplTemplateRow
      .replace('{{rango}}', tpl.rango)
      .replace('{{estado}}', tpl.estado)
      .replace('{{id}}', tpl.id)
    ).join('');
  }

  templatesTable.addEventListener('click', event => {
    const target = event.target;
    if (target.matches('button[data-id]')) {
      alert(`Aplicar plantilla ${target.dataset.id}`);
    }
  });
</script>
<?php renderLegal(); ?>
<?php renderFooter(); ?>
