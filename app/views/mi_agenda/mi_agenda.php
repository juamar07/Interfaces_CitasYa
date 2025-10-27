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
    font-family:'Open Sans',sans-serif;
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
  h1{ text-align:center; margin:0 0 8px; font-weight:700; }
  h2{ margin:18px 0 8px; font-weight:700; color:#000; }
  .muted{ color:#666; font-size:14px; }

  table{ width:100%; border-collapse: collapse; }
  th, td{ padding:10px; border-bottom:1px solid #eee; text-align:left; }
  th{ background:#f6f7fb; font-weight:700; color:#233247; }

  .btn{
    display:block; border:none; border-radius:6px; padding:12px 18px; color:#fff; font-weight:700; cursor:pointer;
    transition: background-color .2s, transform .2s; width:100%; margin:10px 0; font-size:16px;
  }
  .btn:hover{ transform: translateY(-1px); }
  .btn-green{ background:#66bb6a; } .btn-green:hover{ background:#43a047; }
  .btn-blue{  background:#5c6bc0; } .btn-blue:hover{  background:#3f51b5; }

  .wide75{ width:75%; margin:12px auto; }
  @media (max-width:768px){ .wide75{ width:100%; } }

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

  .card{
    border:1px dashed #cfd6e4; border-radius:8px; padding:12px; background:#fbfcff;
  }
  .grid-two{ display:grid; grid-template-columns:1fr 1fr; gap:12px; }
  @media (max-width:768px){ .grid-two{ grid-template-columns:1fr; } }
</style>
<?php
$styles = ob_get_clean();
renderHeader('Mi agenda', $styles);
renderBanner('Mi agenda', '/organizar-agenda');
?>
<div class="container">
  <h1 id="welcome">Mi agenda</h1>

  <section>
    <h2>Citas agendadas</h2>
    <div class="card">
      <table id="tblBookings">
        <thead>
          <tr>
            <th>Asistente</th>
            <th>Fecha</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Servicio</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="5" class="muted">No hay citas para mostrar.</td></tr>
        </tbody>
      </table>
    </div>
  </section>

  <section class="wide75" style="margin-top:6px;">
    <button id="btnICS" class="btn btn-blue">Conectar con calendario virtual</button>
  </section>

  <section>
    <h2>Mi horario</h2>
    <div id="scheduleView" class="card">
      <div class="muted">No hay un horario publicado aún.</div>
    </div>
    <div class="muted" id="rangeInfo" style="margin-top:6px;"></div>
  </section>

  <section class="wide75">
    <button id="btnEdit" class="btn btn-green">Editar agenda</button>
  </section>
</div>

<script>
  const LS_BIZ   = 'cy_businesses';
  const LS_SCH   = 'cy_schedules';
  const LS_BOOK  = 'cy_bookings';
  const LS_CTX   = 'cy_lastAgendaView';
  const LS_EDIT  = 'cy_lastAgendaEdit';

  const bookingsTable = document.getElementById('tblBookings').querySelector('tbody');
  const scheduleView = document.getElementById('scheduleView');
  const rangeInfo = document.getElementById('rangeInfo');

  const demoSchedule = {
    rango: { inicio: '2025-02-10', fin: '2025-03-10' },
    dias: [
      { dia: 'Lunes', trabaja: true, inicio: '08:00', fin: '17:00', almuerzo: '12:00-13:00' },
      { dia: 'Martes', trabaja: true, inicio: '08:00', fin: '17:00', almuerzo: '12:00-13:00' },
      { dia: 'Miércoles', trabaja: true, inicio: '09:00', fin: '18:00', almuerzo: '13:00-14:00' },
      { dia: 'Jueves', trabaja: true, inicio: '10:00', fin: '19:00', almuerzo: null },
      { dia: 'Viernes', trabaja: true, inicio: '08:00', fin: '16:00', almuerzo: '12:30-13:00' },
      { dia: 'Sábado', trabaja: false },
      { dia: 'Domingo', trabaja: false }
    ]
  };

  const demoBookings = [
    { asistente:'Juan Gómez', fecha:'2025-02-15', inicio:'09:00', fin:'09:50', servicio:'Corte y barba' },
    { asistente:'Laura Pérez', fecha:'2025-02-16', inicio:'11:00', fin:'11:30', servicio:'Corte clásico' },
    { asistente:'Carlos Ruiz', fecha:'2025-02-17', inicio:'14:00', fin:'14:40', servicio:'Tratamiento capilar' }
  ];

  function renderBookings(items) {
    if (!items.length) {
      bookingsTable.innerHTML = '<tr><td colspan="5" class="muted">No hay citas para mostrar.</td></tr>';
      return;
    }
    bookingsTable.innerHTML = items.map(item => `
      <tr>
        <td>${item.asistente}</td>
        <td>${new Date(item.fecha + 'T00:00').toLocaleDateString('es-CO', { timeZone:'America/Bogota' })}</td>
        <td>${item.inicio}</td>
        <td>${item.fin}</td>
        <td>${item.servicio}</td>
      </tr>
    `).join('');
  }

  function renderSchedule(sched) {
    if (!sched) {
      scheduleView.innerHTML = '<div class="muted">No hay un horario publicado aún.</div>';
      rangeInfo.textContent = '';
      return;
    }

    rangeInfo.textContent = `Vigente del ${new Date(sched.rango.inicio + 'T00:00').toLocaleDateString('es-CO', { timeZone:'America/Bogota' })} al ${new Date(sched.rango.fin + 'T00:00').toLocaleDateString('es-CO', { timeZone:'America/Bogota' })}`;
    scheduleView.innerHTML = `
      <div class="grid-two">
        ${sched.dias.map(dia => `
          <div class="card" style="border:1px solid #dce3f0; background:#fff;">
            <strong>${dia.dia}</strong>
            <div class="muted">
              ${dia.trabaja ? `Horario: ${dia.inicio} - ${dia.fin}` : 'No trabaja este día'}
            </div>
            ${dia.trabaja && dia.almuerzo ? `<div class="muted">Almuerzo: ${dia.almuerzo}</div>` : ''}
          </div>
        `).join('')}
      </div>
    `;
  }

  renderBookings(demoBookings);
  renderSchedule(demoSchedule);

  document.getElementById('btnICS').addEventListener('click', () => {
    alert('Se descargará un archivo .ics con tus citas.');
  });

  document.getElementById('btnEdit').addEventListener('click', () => {
    window.location.href = '/organizar-agenda';
  });
</script>
<?php renderLegal(); ?>
<?php renderFooter(); ?>
