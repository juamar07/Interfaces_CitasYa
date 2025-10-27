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
  h2{ margin:20px 0 12px; font-weight:700; color:#000; }
  h3{ margin:12px 0 8px; color:#233247; }

  label{ display:block; margin:8px 0 4px; color:#003366; }
  input[type="text"], input[type="number"], select, textarea{
    width:100%; padding:10px; border:2px solid #ddd; border-radius:6px; font-size:16px;
    transition: border-color .2s; box-sizing:border-box;
  }
  textarea{ min-height:210px; resize:vertical; }

  .muted{ color:#666; font-size:14px; }
  .row{ display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
  .row-3{ display:grid; grid-template-columns: 1fr 1fr 1fr; gap:12px; }

  .packages{
    border:1px dashed #bdbdbd; border-radius:8px; padding:12px; background:#fafafa; margin:8px 0 12px;
  }
  .packages ul{ margin:6px 0 0 18px; }
  .price{ font-weight:700; }

  .btn{
    display:block; border:none; border-radius:6px; padding:12px 18px; color:#fff; font-weight:700; cursor:pointer;
    transition: background-color .2s, transform .2s; width:100%; margin:10px 0; font-size:16px;
  }
  .btn:hover{ transform: translateY(-1px); }
  .btn-green{ background:#66bb6a; } .btn-green:hover{ background:#43a047; }
  .btn-blue{ background:#5c6bc0; } .btn-blue:hover{ background:#3f51b5; }
  .btn-red{ background:#ef5350; } .btn-red:hover{ background:#e53935; }
  .btn-slim{ width:min(640px, 75%); margin:12px auto; }

  .radio-group{ display:flex; align-items:center; gap:10px; }

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
renderHeader('Pagos - Citas Ya', $styles);
renderBanner('Pagos');
?>
<div class="container">
  <h1>Comprar tokens</h1>

  <p class="muted">Cada <strong>30 min</strong> de cita consume <strong>1 token</strong>. Puedes escoger un paquete o indicar una cantidad manual.</p>

  <div class="packages">
    <strong>Paquetes disponibles</strong>
    <ul>
      <li>50 tokens — <span class="price">$21.000</span> ( <em>$420/token</em> )</li>
      <li>100 tokens — <span class="price">$34.000</span> ( <em>$340/token</em> )</li>
      <li>150 tokens — <span class="price">$42.000</span> ( <em>$280/token</em> )</li>
      <li>200 tokens — <span class="price">$44.000</span> ( <em>$220/token</em> )</li>
    </ul>
    <p class="muted" style="margin:8px 0 0;">¿Más de 200 tokens? Te conviene <strong>Citas Ya Plus</strong> ($47.000/mes, tokens ilimitados).</p>
  </div>

  <section>
    <h2>Selecciona tu compra</h2>
    <div class="row">
      <div>
        <label for="paquete">Paquete</label>
        <select id="paquete">
          <option value="">— Elegir paquete —</option>
          <option value="50">50 tokens — $21.000</option>
          <option value="100">100 tokens — $34.000</option>
          <option value="150">150 tokens — $42.000</option>
          <option value="200">200 tokens — $44.000</option>
        </select>
      </div>
      <div>
        <label for="tokens_manual">Cantidad manual (tokens)</label>
        <input type="number" id="tokens_manual" min="1" step="1" placeholder="Ej: 65" />
        <small class="muted">El precio por token usará la tarifa del paquete anterior (≤50: $420; 51–100: $340; 101–150: $280; 151–200: $220).</small>
      </div>
    </div>
  </section>

  <section style="margin-top:8px;">
    <div class="radio-group">
      <input type="checkbox" id="plus" />
      <label for="plus"><strong>Citas Ya Plus</strong> (tokens ilimitados 1 mes) — <span class="price">$47.000</span></label>
    </div>
    <small class="muted">Marca la casilla si deseas agregar la suscripción Plus.</small>
  </section>

  <section class="btn-slim">
    <button id="btnPay" class="btn btn-green">Proceder con pago</button>
  </section>

  <section>
    <h2>Historial reciente</h2>
    <table style="width:100%; border-collapse:collapse;">
      <thead>
        <tr style="background:#f5f5f5;">
          <th style="text-align:left; padding:8px;">Fecha</th>
          <th style="text-align:left; padding:8px;">Concepto</th>
          <th style="text-align:left; padding:8px;">Tokens</th>
          <th style="text-align:left; padding:8px;">Monto</th>
          <th style="text-align:left; padding:8px;">Estado</th>
        </tr>
      </thead>
      <tbody id="history">
        <tr><td colspan="5" class="muted" style="padding:10px;">Sin movimientos recientes.</td></tr>
      </tbody>
    </table>
  </section>
</div>

<script>
  const historyEl = document.getElementById('history');
  const btnPay = document.getElementById('btnPay');
  const paquete = document.getElementById('paquete');
  const tokensManual = document.getElementById('tokens_manual');
  const plus = document.getElementById('plus');

  const historyData = [
    { fecha:'2025-02-10', concepto:'Compra 100 tokens', tokens:100, monto:34000, estado:'Pagado' },
    { fecha:'2025-02-05', concepto:'Suscripción Plus', tokens:'Ilimitados', monto:47000, estado:'Pagado' }
  ];

  function renderHistory() {
    if (!historyData.length) {
      historyEl.innerHTML = '<tr><td colspan="5" class="muted" style="padding:10px;">Sin movimientos recientes.</td></tr>';
      return;
    }
    historyEl.innerHTML = historyData.map(item => `
      <tr>
        <td style="padding:8px;">${new Date(item.fecha + 'T00:00').toLocaleDateString('es-CO', { timeZone:'America/Bogota' })}</td>
        <td style="padding:8px;">${item.concepto}</td>
        <td style="padding:8px;">${item.tokens}</td>
        <td style="padding:8px;">$${item.monto.toLocaleString('es-CO')}</td>
        <td style="padding:8px;">${item.estado}</td>
      </tr>
    `).join('');
  }

  renderHistory();

  btnPay.addEventListener('click', () => {
    const selectedPackage = paquete.value;
    const manualTokens = parseInt(tokensManual.value || '0', 10);

    if (!selectedPackage && !manualTokens && !plus.checked) {
      alert('Selecciona un paquete, ingresa tokens manuales o activa Citas Ya Plus.');
      return;
    }

    const resumen = [];
    if (selectedPackage) {
      resumen.push(`Paquete de ${selectedPackage} tokens`);
    }
    if (manualTokens) {
      resumen.push(`${manualTokens} tokens manuales`);
    }
    if (plus.checked) {
      resumen.push('Suscripción Citas Ya Plus');
    }

    alert(`Se iniciará el pago para: ${resumen.join(', ')}.`);
  });
</script>
<?php renderLegal(); ?>
<?php renderFooter(); ?>
