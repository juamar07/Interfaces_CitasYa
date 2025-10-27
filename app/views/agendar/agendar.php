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

  .app-banner{ position:fixed; top:0; left:0; right:0; height:var(--banner-h); z-index:9999; background:transparent; }
  .app-banner .banner-box{
    height:100%;
    width:min(calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl)), calc(100% - var(--page-sidepad)*2));
    margin:0 auto; background:var(--banner-bg); border-bottom:1px solid rgba(0,0,0,.06); border-radius:10px;
    display:flex; align-items:center; transition:background-color .2s ease;
  }
  .app-banner .banner-box:hover,.app-banner .banner-box:focus-within{ background:var(--banner-bg-hover); }
  .app-banner .banner-inner{ display:grid; grid-template-columns: 1fr auto 1fr; align-items:center; gap:8px; width:100%; padding:0 12px; }
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
    margin:18px auto 24px; padding:10px 12px;
    max-width: calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl));
    text-align:center; color:#666; font-size:14px; line-height:1.35;
  }

  .chips{ display:flex; flex-wrap:wrap; gap:8px; margin:10px 0; }
  .chip{ padding:8px 12px; border-radius:20px; background:#e0e7ff; color:#233247; font-size:14px; }
  .map-preview{ width:100%; height:280px; border-radius:12px; border:1px solid #d0d6e2; margin-top:12px; background:#cfd8dc; display:flex; align-items:center; justify-content:center; color:#37474f; font-weight:600; }
  .summary-card{ margin-top:16px; background:#f5f7fb; border-radius:10px; padding:16px; border:1px solid #dce3f0; }
  .summary-card h2{ margin:0 0 10px; font-size:18px; color:#233247; }
  .summary-item{ display:flex; justify-content:space-between; margin-bottom:6px; font-size:15px; }
  .summary-item span{ font-weight:600; }
</style>
<?php
$styles = ob_get_clean();
renderHeader('Programar una cita', $styles);
renderBanner('Programación de Citas');
?>
<div class="container">
  <h1>Programación de Citas</h1>

  <section>
    <label for="attendee">Ingrese nombre completo del asistente</label>
    <input type="text" id="attendee" placeholder="Ej: Juan Martín Betancur">
  </section>

  <section>
    <label for="biz">Ingrese el nombre del establecimiento</label>
    <input type="text" id="biz" list="businessList" placeholder="Ej: Barbería Central">
    <datalist id="businessList"></datalist>

    <div style="display:flex; align-items:center; gap:10px; margin-top:8px;">
      <label style="display:flex; align-items:center; gap:8px; margin:0;">
        <input type="checkbox" id="dontKnow"> ¿No recuerda el nombre del establecimiento?
      </label>
      <button type="button" id="btnMap" class="btn btn-blue" style="width:auto; display:none; margin:0;">Buscar por mapa</button>
    </div>
    <small class="muted">La búsqueda ignora mayúsculas y acentos.</small>
  </section>

  <section>
    <label for="service">Seleccione el servicio</label>
    <select id="service" disabled>
      <option value="">— Seleccione —</option>
    </select>
    <small class="muted" id="svcDur">Duración: —</small>
  </section>

  <section>
    <label for="staff">Seleccione el barbero</label>
    <select id="staff" disabled>
      <option value="">— Seleccione —</option>
    </select>
  </section>

  <section>
    <div class="row">
      <div>
        <label for="date">Seleccione la fecha de la cita</label>
        <input type="date" id="date">
      </div>
      <div>
        <label for="timeSel">Seleccione la hora de la cita</label>
        <select id="timeSel" disabled>
          <option value="">— Selecciona fecha, servicio y barbero —</option>
        </select>
        <small class="muted" id="timeHelp"></small>
      </div>
    </div>
  </section>

  <section>
    <label for="notes">Notas adicionales (opcional)</label>
    <textarea id="notes" rows="3" placeholder="Ej: Llegaré 5 minutos antes, prefiero estilo clásico."></textarea>
  </section>

  <section class="summary-card" aria-live="polite">
    <h2>Resumen de la cita</h2>
    <div class="summary-item"><span>Asistente:</span> <span id="sumName">—</span></div>
    <div class="summary-item"><span>Establecimiento:</span> <span id="sumBiz">—</span></div>
    <div class="summary-item"><span>Servicio:</span> <span id="sumSvc">—</span></div>
    <div class="summary-item"><span>Barbero:</span> <span id="sumStaff">—</span></div>
    <div class="summary-item"><span>Fecha:</span> <span id="sumDate">—</span></div>
    <div class="summary-item"><span>Hora:</span> <span id="sumTime">—</span></div>
    <div class="summary-item"><span>Duración:</span> <span id="sumDur">—</span></div>
  </section>

  <div class="wide75">
    <button class="btn btn-green" id="btnConfirm">Confirmar cita</button>
  </div>

  <template id="tplSlots">
    <option value="{{value}}">{{label}}</option>
  </template>
  <template id="tplStaff">
    <option value="{{value}}">{{label}}</option>
  </template>
  <template id="tplSvc">
    <option value="{{value}}" data-duration="{{duration}}" data-tokens="{{tokens}}">{{label}}</option>
  </template>
</div>

<div id="mapModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.65); align-items:center; justify-content:center;">
  <div style="background:#fff; padding:20px; border-radius:12px; width:min(760px, 90vw);">
    <h2 style="margin-top:0;">Buscar establecimiento por mapa</h2>
    <div class="map-preview">Mapa interactivo (placeholder)</div>
    <div style="text-align:right; margin-top:12px;">
      <button class="btn btn-blue" style="width:auto; display:inline-block;" id="mapClose">Cerrar</button>
    </div>
  </div>
</div>

<script>
  const businesses = [
    { id:1, nombre:'Barbería Central', servicios:[
      { id:11, nombre:'Corte clásico', duracion:30, tokens:1 },
      { id:12, nombre:'Corte y barba', duracion:50, tokens:2 },
      { id:13, nombre:'Afeitado premium', duracion:40, tokens:2 }
    ], personal:[
      { id:101, nombre:'Carlos Mejía' },
      { id:102, nombre:'Juan Pérez' }
    ] },
    { id:2, nombre:'Estética Caballeros', servicios:[
      { id:21, nombre:'Corte ejecutivo', duracion:30, tokens:1 },
      { id:22, nombre:'Corte + tratamiento capilar', duracion:60, tokens:2 }
    ], personal:[
      { id:201, nombre:'Pedro Gómez' },
      { id:202, nombre:'Sergio Ramírez' }
    ] }
  ];

  const availableSlots = {
    '1-101': {
      '2025-02-15': ['09:00','09:30','10:00','10:30','11:00','15:30','16:00'],
      '2025-02-16': ['10:00','10:30','11:00','11:30']
    },
    '1-102': {
      '2025-02-15': ['08:00','08:30','09:00','09:30','10:00','17:30'],
      '2025-02-16': ['09:30','10:00','10:30','11:00']
    },
    '2-201': {
      '2025-02-16': ['12:00','12:30','13:00','16:30']
    }
  };

  const bizInput = document.getElementById('biz');
  const bizDatalist = document.getElementById('businessList');
  const svcSelect = document.getElementById('service');
  const staffSelect = document.getElementById('staff');
  const dateInput = document.getElementById('date');
  const timeSelect = document.getElementById('timeSel');
  const notes = document.getElementById('notes');
  const dontKnow = document.getElementById('dontKnow');
  const btnMap = document.getElementById('btnMap');
  const btnConfirm = document.getElementById('btnConfirm');
  const sumName = document.getElementById('sumName');
  const sumBiz = document.getElementById('sumBiz');
  const sumSvc = document.getElementById('sumSvc');
  const sumStaff = document.getElementById('sumStaff');
  const sumDate = document.getElementById('sumDate');
  const sumTime = document.getElementById('sumTime');
  const sumDur = document.getElementById('sumDur');
  const svcDur = document.getElementById('svcDur');
  const timeHelp = document.getElementById('timeHelp');
  const tplSlots = document.getElementById('tplSlots').innerHTML;
  const tplStaff = document.getElementById('tplStaff').innerHTML;
  const tplSvc = document.getElementById('tplSvc').innerHTML;

  function cleanString(str) {
    return str.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase();
  }

  const bizOptions = businesses.map(b => `<option value="${b.nombre}"></option>`).join('');
  bizDatalist.innerHTML = bizOptions;

  dontKnow.addEventListener('change', () => {
    if (dontKnow.checked) {
      btnMap.style.display = 'inline-block';
      bizInput.disabled = true;
      svcSelect.disabled = true;
      staffSelect.disabled = true;
      timeSelect.disabled = true;
      sumBiz.textContent = 'Buscar por mapa';
    } else {
      btnMap.style.display = 'none';
      bizInput.disabled = false;
      resetSelectors();
      sumBiz.textContent = '—';
    }
  });

  let selectedBiz = null;
  let selectedSvc = null;
  let selectedStaff = null;

  bizInput.addEventListener('input', () => {
    const value = bizInput.value.trim();
    selectedBiz = null;

    if (!value) {
      resetSelectors();
      return;
    }

    const normalized = cleanString(value);
    selectedBiz = businesses.find(b => cleanString(b.nombre) === normalized) || null;

    if (!selectedBiz) {
      resetSelectors();
      return;
    }

    sumBiz.textContent = selectedBiz.nombre;
    populateServices(selectedBiz.servicios);
    populateStaff(selectedBiz.personal);
  });

  svcSelect.addEventListener('change', () => {
    const opt = svcSelect.selectedOptions[0];
    if (!opt) {
      selectedSvc = null;
      svcDur.textContent = 'Duración: —';
      updateSummary();
      return;
    }
    selectedSvc = {
      id: opt.value,
      nombre: opt.textContent,
      duracion: parseInt(opt.dataset.duration, 10),
      tokens: parseInt(opt.dataset.tokens, 10)
    };
    svcDur.textContent = `Duración: ${selectedSvc.duracion} minutos · Tokens: ${selectedSvc.tokens}`;
    updateSummary();
    generateSlots();
  });

  staffSelect.addEventListener('change', () => {
    const opt = staffSelect.selectedOptions[0];
    selectedStaff = opt ? { id: opt.value, nombre: opt.textContent } : null;
    updateSummary();
    generateSlots();
  });

  dateInput.addEventListener('change', () => {
    updateSummary();
    generateSlots();
  });

  document.getElementById('attendee').addEventListener('input', updateSummary);
  timeSelect.addEventListener('change', updateSummary);

  function resetSelectors() {
    svcSelect.innerHTML = '<option value="">— Seleccione —</option>';
    svcSelect.disabled = true;
    staffSelect.innerHTML = '<option value="">— Seleccione —</option>';
    staffSelect.disabled = true;
    timeSelect.innerHTML = '<option value="">— Selecciona fecha, servicio y barbero —</option>';
    timeSelect.disabled = true;
    svcDur.textContent = 'Duración: —';
    timeHelp.textContent = '';
    selectedSvc = null;
    selectedStaff = null;
    sumSvc.textContent = '—';
    sumStaff.textContent = '—';
    sumTime.textContent = '—';
    sumDur.textContent = '—';
  }

  function populateServices(servicios) {
    svcSelect.disabled = false;
    svcSelect.innerHTML = '<option value="">— Seleccione —</option>' + servicios.map(s =>
      tplSvc.replace('{{value}}', s.id)
            .replace('{{label}}', s.nombre)
            .replace('{{duration}}', s.duracion)
            .replace('{{tokens}}', s.tokens)
    ).join('');
  }

  function populateStaff(personal) {
    staffSelect.disabled = false;
    staffSelect.innerHTML = '<option value="">— Seleccione —</option>' + personal.map(p =>
      tplStaff.replace('{{value}}', p.id).replace('{{label}}', p.nombre)
    ).join('');
  }

  function generateSlots() {
    if (!selectedBiz || !selectedStaff || !selectedSvc || !dateInput.value) {
      timeSelect.innerHTML = '<option value="">— Selecciona fecha, servicio y barbero —</option>';
      timeSelect.disabled = true;
      timeHelp.textContent = '';
      sumTime.textContent = '—';
      return;
    }

    const key = `${selectedBiz.id}-${selectedStaff.id}`;
    const slotsForDay = (availableSlots[key] && availableSlots[key][dateInput.value]) || [];

    if (!slotsForDay.length) {
      timeSelect.innerHTML = '<option value="">— Sin disponibilidad —</option>';
      timeSelect.disabled = true;
      timeHelp.textContent = 'No hay disponibilidad para la combinación seleccionada.';
      sumTime.textContent = '—';
      return;
    }

    timeSelect.disabled = false;
    timeSelect.innerHTML = '<option value="">— Seleccione un horario —</option>' + slotsForDay.map(slot =>
      tplSlots.replace('{{value}}', slot).replace('{{label}}', `${slot} hrs`)
    ).join('');
    timeHelp.textContent = `El servicio requiere ${selectedSvc.duracion} minutos.`;
  }

  function updateSummary() {
    const attendee = document.getElementById('attendee').value.trim();
    sumName.textContent = attendee || '—';
    if (!selectedBiz) {
      sumBiz.textContent = dontKnow.checked ? 'Buscar por mapa' : '—';
    }
    sumSvc.textContent = selectedSvc ? selectedSvc.nombre : '—';
    sumStaff.textContent = selectedStaff ? selectedStaff.nombre : '—';
    sumDate.textContent = dateInput.value ? new Date(dateInput.value + 'T00:00').toLocaleDateString('es-CO', { timeZone:'America/Bogota' }) : '—';
    sumTime.textContent = timeSelect.value ? `${timeSelect.value} hrs` : '—';
    sumDur.textContent = selectedSvc ? `${selectedSvc.duracion} minutos` : '—';
  }

  const mapModal = document.getElementById('mapModal');
  btnMap.addEventListener('click', () => {
    mapModal.style.display = 'flex';
  });
  document.getElementById('mapClose').addEventListener('click', () => {
    mapModal.style.display = 'none';
  });
  mapModal.addEventListener('click', (ev) => {
    if (ev.target === mapModal) {
      mapModal.style.display = 'none';
    }
  });

  btnConfirm.addEventListener('click', () => {
    if (!selectedBiz || !selectedSvc || !selectedStaff || !dateInput.value || !timeSelect.value) {
      alert('Completa todos los campos antes de confirmar la cita.');
      return;
    }

    const summary = `Cita confirmada para ${sumName.textContent} en ${sumBiz.textContent} con ${sumStaff.textContent} el ${sumDate.textContent} a las ${sumTime.textContent}.`;
    alert(summary);
  });
</script>
<?php renderLegal(); ?>
<?php renderFooter(); ?>
