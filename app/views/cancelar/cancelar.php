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
  h2{ margin:18px 0 10px; font-weight:700; color:#000; }
  h3{ margin:12px 0 8px; color:#233247; }
  label{ display:block; margin:8px 0 4px; color:#003366; }
  .muted{ color:#666; font-size:14px; }

  input[type="text"], input[type="date"], select, textarea{
    width:100%; padding:10px; border:2px solid #ddd; border-radius:6px; font-size:16px;
    transition: border-color .2s; box-sizing:border-box;
  }
  input:focus, select:focus, textarea:focus{ outline:none; border-color:#7da2a9; }

  .row{ display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
  .two-btns{ display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
  .wide75{ width:75%; margin:12px auto; }
  @media (max-width:768px){ .row, .two-btns, .wide75{ grid-template-columns:1fr; width:100%; } }

  .btn{
    display:block; border:none; border-radius:6px; padding:12px 18px; color:#fff; font-weight:700; cursor:pointer;
    transition: background-color .2s, transform .2s; width:100%; margin:10px 0; font-size:16px;
  }
  .btn:hover{ transform: translateY(-1px); }
  .btn-green{ background:#66bb6a; } .btn-green:hover{ background:#43a047; }
  .btn-blue{  background:#5c6bc0; } .btn-blue:hover{  background:#3f51b5; }
  .btn-red{   background:#ef5350; } .btn-red:hover{   background:#e53935; }

  .card{
    border:1px dashed #bdbdbd; border-radius:8px; padding:12px; background:#fafafa; margin:8px 0 12px;
  }

  textarea{ min-height:150px; resize:vertical; }

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
</style>
<?php
$styles = ob_get_clean();
renderHeader('Cancelar o Re-agendar cita', $styles);
renderBanner('Cancelar o Re-agendar');
?>
<div class="container">
  <h1>Cancelar o Re-agendar una cita</h1>

  <section class="row">
    <div>
      <label for="accion">Acción</label>
      <select id="accion">
        <option value="cancelar">Cancelar</option>
        <option value="reagendar">Re-agendar</option>
      </select>
    </div>
    <div>
      <label for="attendee">Nombre del asistente</label>
      <input type="text" id="attendee" list="attendeesList" placeholder="Ej: Juan Martín Betancur">
      <datalist id="attendeesList"></datalist>
      <small class="muted">Escribe el nombre tal como lo registraste; la búsqueda ignora mayúsculas y acentos.</small>
    </div>
  </section>

  <section id="citasSection" style="display:none;">
    <h2>Selecciona la cita</h2>
    <div id="citasList" class="card"></div>
  </section>

  <section id="cancelBlock" style="display:none;">
    <h2>Resumen de la cita</h2>
    <textarea id="cancelSummary" readonly></textarea>
    <div class="two-btns">
      <button id="btnCancelar" class="btn btn-red">Confirmar cancelación</button>
      <button id="btnClear" class="btn btn-blue">Limpiar</button>
    </div>
  </section>

  <section id="reBlock" style="display:none;">
    <h2>Re-agendar la cita</h2>
    <div class="card">
      <h3>Selecciona el nuevo horario</h3>
      <div class="row">
        <div>
          <label for="newDate">Nueva fecha</label>
          <input type="date" id="newDate">
        </div>
        <div>
          <label for="newTime">Nuevo horario</label>
          <select id="newTime" disabled>
            <option value="">— Selecciona fecha —</option>
          </select>
        </div>
      </div>
      <small class="muted">Los horarios se generan según la disponibilidad del barbero y la duración del servicio.</small>
    </div>
    <div class="two-btns">
      <button id="btnReagendar" class="btn btn-green">Confirmar nuevo horario</button>
      <button id="btnCancelarRe" class="btn btn-blue">Cancelar re-agendo</button>
    </div>
  </section>

  <section>
    <h2>Historial de acciones recientes</h2>
    <div class="card" id="history">
      <p class="muted" style="margin:0;">Aún no registras acciones.</p>
    </div>
  </section>
</div>

<template id="tplCita">
  <article class="card cita-card" data-id="{{id}}" role="button" tabindex="0">
    <h3>{{cliente}}</h3>
    <p style="margin:4px 0;"><strong>Servicio:</strong> {{servicio}}</p>
    <p style="margin:4px 0;"><strong>Barbero:</strong> {{barbero}}</p>
    <p style="margin:4px 0;"><strong>Fecha:</strong> {{fecha}} &bull; {{hora}}</p>
    <p style="margin:4px 0;" class="muted">Duración: {{duracion}} minutos &bull; Estado: {{estado}}</p>
  </article>
</template>

<script>
  const asistentes = [
    { nombre:'Juan Martín Betancur', citas:[
      { id:1, servicio:'Corte clásico', barbero:'Carlos Mejía', fecha:'2025-02-15', hora:'09:00', duracion:30, estado:'Reservada' },
      { id:2, servicio:'Corte y barba', barbero:'Juan Pérez', fecha:'2025-02-20', hora:'11:00', duracion:50, estado:'Reservada' }
    ] },
    { nombre:'Laura Díaz', citas:[
      { id:3, servicio:'Tratamiento capilar', barbero:'Pedro Gómez', fecha:'2025-02-18', hora:'15:30', duracion:60, estado:'Reservada' }
    ] }
  ];

  const historial = [];

  const attendeesList = document.getElementById('attendeesList');
  attendeesList.innerHTML = asistentes.map(a => `<option value="${a.nombre}"></option>`).join('');

  const accionSelect = document.getElementById('accion');
  const attendeeInput = document.getElementById('attendee');
  const citasSection = document.getElementById('citasSection');
  const citasList = document.getElementById('citasList');
  const cancelBlock = document.getElementById('cancelBlock');
  const cancelSummary = document.getElementById('cancelSummary');
  const btnCancelar = document.getElementById('btnCancelar');
  const btnClear = document.getElementById('btnClear');
  const reBlock = document.getElementById('reBlock');
  const newDate = document.getElementById('newDate');
  const newTime = document.getElementById('newTime');
  const btnReagendar = document.getElementById('btnReagendar');
  const btnCancelarRe = document.getElementById('btnCancelarRe');
  const historyEl = document.getElementById('history');
  const tplCita = document.getElementById('tplCita').innerHTML;

  let citasEncontradas = [];
  let citaSeleccionada = null;

  function normalizar(valor) {
    return valor.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase();
  }

  attendeeInput.addEventListener('input', () => {
    const valor = attendeeInput.value.trim();
    citaSeleccionada = null;
    cancelBlock.style.display = 'none';
    reBlock.style.display = 'none';

    if (!valor) {
      citasSection.style.display = 'none';
      citasList.innerHTML = '';
      return;
    }

    const encontrado = asistentes.find(a => normalizar(a.nombre) === normalizar(valor));
    if (!encontrado) {
      citasSection.style.display = 'none';
      citasList.innerHTML = '<p class="muted" style="margin:0;">No se encontraron citas asociadas.</p>';
      return;
    }

    citasEncontradas = encontrado.citas;
    renderCitas();
  });

  function renderCitas() {
    if (!citasEncontradas.length) {
      citasSection.style.display = 'none';
      citasList.innerHTML = '<p class="muted" style="margin:0;">No se encontraron citas.</p>';
      return;
    }

    citasSection.style.display = 'block';
    citasList.innerHTML = citasEncontradas.map(c => tplCita
      .replace(/{{id}}/g, c.id)
      .replace('{{cliente}}', attendeeInput.value)
      .replace('{{servicio}}', c.servicio)
      .replace('{{barbero}}', c.barbero)
      .replace('{{fecha}}', new Date(c.fecha + 'T00:00').toLocaleDateString('es-CO', { timeZone:'America/Bogota' }))
      .replace('{{hora}}', c.hora)
      .replace('{{duracion}}', c.duracion)
      .replace('{{estado}}', c.estado)
    ).join('');

    citasList.querySelectorAll('.cita-card').forEach(card => {
      card.addEventListener('click', () => seleccionarCita(card.dataset.id));
      card.addEventListener('keypress', (ev) => {
        if (ev.key === 'Enter' || ev.key === ' ') {
          ev.preventDefault();
          seleccionarCita(card.dataset.id);
        }
      });
    });
  }

  function seleccionarCita(id) {
    citaSeleccionada = citasEncontradas.find(c => String(c.id) === String(id)) || null;
    if (!citaSeleccionada) {
      return;
    }

    cancelSummary.value = `Cita con ${citaSeleccionada.barbero}\nServicio: ${citaSeleccionada.servicio}\nFecha: ${new Date(citaSeleccionada.fecha + 'T00:00').toLocaleDateString('es-CO', { timeZone:'America/Bogota' })} ${citaSeleccionada.hora}\nDuración: ${citaSeleccionada.duracion} minutos`;

    if (accionSelect.value === 'cancelar') {
      cancelBlock.style.display = 'block';
      reBlock.style.display = 'none';
    } else {
      cancelBlock.style.display = 'none';
      reBlock.style.display = 'block';
      newDate.value = '';
      newTime.innerHTML = '<option value="">— Selecciona fecha —</option>';
      newTime.disabled = true;
    }
  }

  accionSelect.addEventListener('change', () => {
    if (citaSeleccionada) {
      seleccionarCita(citaSeleccionada.id);
    }
  });

  newDate.addEventListener('change', () => {
    if (!newDate.value) {
      newTime.innerHTML = '<option value="">— Selecciona fecha —</option>';
      newTime.disabled = true;
      return;
    }

    const opciones = generarHorarios(newDate.value);
    if (!opciones.length) {
      newTime.innerHTML = '<option value="">— Sin disponibilidad —</option>';
      newTime.disabled = true;
      return;
    }

    newTime.disabled = false;
    newTime.innerHTML = '<option value="">— Selecciona horario —</option>' + opciones.map(h => `<option value="${h}">${h}</option>`).join('');
  });

  function generarHorarios(fecha) {
    const base = ['08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','15:30','16:00'];
    return base.filter((_, idx) => (fecha.charCodeAt(fecha.length - 1) + idx) % 2 === 0);
  }

  function registrarAccion(texto) {
    const fecha = new Date();
    historial.unshift({ fecha, texto });
    historyEl.innerHTML = historial.map(item => `<p style="margin:6px 0;">${item.texto}<br><span class="muted">${item.fecha.toLocaleString('es-CO', { timeZone:'America/Bogota' })}</span></p>`).join('');
  }

  btnCancelar.addEventListener('click', () => {
    if (!citaSeleccionada) {
      alert('Selecciona una cita antes de cancelarla.');
      return;
    }
    registrarAccion(`Se canceló la cita ${citaSeleccionada.id} de ${attendeeInput.value}.`);
    alert('La cita se ha cancelado.');
  });

  btnClear.addEventListener('click', () => {
    cancelSummary.value = '';
    citasSection.style.display = 'none';
    citasList.innerHTML = '';
    attendeeInput.value = '';
    cancelBlock.style.display = 'none';
    reBlock.style.display = 'none';
    citaSeleccionada = null;
  });

  btnReagendar.addEventListener('click', () => {
    if (!citaSeleccionada || !newDate.value || !newTime.value) {
      alert('Selecciona la cita y el nuevo horario.');
      return;
    }
    registrarAccion(`Se re-agendó la cita ${citaSeleccionada.id} para el ${newDate.value} a las ${newTime.value}.`);
    alert('La cita se re-agendó exitosamente.');
  });

  btnCancelarRe.addEventListener('click', () => {
    reBlock.style.display = 'none';
    newDate.value = '';
    newTime.innerHTML = '<option value="">— Selecciona fecha —</option>';
    newTime.disabled = true;
  });
</script>
<?php renderLegal(); ?>
<?php renderFooter(); ?>
