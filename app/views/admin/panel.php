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
    --container-w: 980px;
    --container-pad: 20px;
    --container-bl: 4px;

    --banner-h: 64px;
    --banner-bg: #e6e9ee;
    --banner-bg-hover: #d7dbe3;

    --blue:#5c6bc0; --blue-h:#3f51b5;
    --green:#66bb6a; --red:#ef5350;
    --ink:#233247;
  }

  *{box-sizing:border-box}
  body{
    font-family:'Open Sans',sans-serif;
    background:#eee; color:#333;
    margin:0;
    padding:20px;
    padding-top: calc(var(--banner-h) + 20px);
  }

  .app-banner{position:fixed; top:0; left:0; right:0; height:var(--banner-h); z-index:999}
  
  .app-banner .banner-box{height:100%; background:var(--banner-bg); border-radius:10px; border-bottom:1px solid rgba(0,0,0,.06); display:flex; align-items:center; transition:.2s; padding:0 12px; width:min(calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl)), calc(100% - 32px)); margin:0 auto;}
  .banner-box:hover{background:var(--banner-bg-hover)}
  .banner-inner{display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:8px; width:100%}
  .banner-title{justify-self:center; font-weight:700; color:var(--ink)}
  .banner-logo{justify-self:end}
  .banner-logo img{width:52px;height:auto}
  .back-button{justify-self:start; display:inline-block; text-decoration:none; font-size:14px; color:var(--blue); padding:8px 12px; border:1px solid var(--blue); border-radius:6px; transition:.2s}
  .back-button:hover{background:var(--blue); color:#fff}

  .container{
    max-width:var(--container-w);
    margin:0 auto;
    background:#fff;
    border-radius:10px;
    padding:var(--container-pad);
    border-left:var(--container-bl) solid var(--blue);
    box-shadow:0 4px 8px rgba(0,0,0,.05)
  }
  h1{margin:0 0 18px; text-align:center}

  .kpis{display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:18px}
  .kpi{background:#f8f9ff; border:1px solid #e5e7f5; border-radius:10px; padding:16px}
  .kpi .val{font-size:30px; font-weight:700; color:#000}
  .kpi .lab{margin-top:6px; color:#555}

  .box{margin-top:16px}
  .subttl{font-weight:700; color:#000; margin:6px 0 10px}
  .dist{margin:8px 0}
  .bar{height:8px;background:#e9edf7;border-radius:6px;overflow:hidden;margin:6px 0}
  .bar>i{display:block;height:100%;width:0;background:var(--blue);border-radius:6px}
  .legend{font-size:12px;color:#666}
  .wrap-flex{display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap}
  .chart{width:220px;height:220px;position:relative;margin:0 auto}
  .pie-legend{text-align:center;margin-top:6px;font-size:14px}
  .dot{display:inline-block;width:10px;height:10px;border-radius:50%;margin-right:6px}
  .dot-green{background:#66bb6a}.dot-red{background:#ef5350}
  .filters{display:flex;gap:14px;align-items:center;flex-wrap:wrap;margin:10px 0}
  .filters .destino{margin-left:auto}

  .cmt{border:1px solid #ececec;border-radius:10px;padding:12px 14px;margin:8px 0; display:grid;grid-template-columns:1fr auto;gap:12px;align-items:center}
  .cmt-head{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
  .stars{color:#f5a623}
  .tag{font-size:12px;padding:2px 8px;border-radius:14px}
  .tag.pos{background:#e9f7ef;color:#1b5e20}
  .tag.neu{background:#eef1f6;color:#445}
  .tag.neg{background:#ffe4e4;color:#b00020}
  .muted{color:#666;font-size:12px}
  .btn{border:none;border-radius:8px;padding:10px 14px;cursor:pointer;color:#fff;background:var(--blue)}
  .btn:hover{background:var(--blue-h)}

  .legal-outside{
    margin:18px auto 24px; padding:10px 12px;
    max-width: calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl));
    text-align:center; color:#666; font-size:14px; line-height:1.35
  }

  @media(max-width:920px){.kpis{grid-template-columns:repeat(2,1fr)}}
  @media(max-width:560px){.kpis{grid-template-columns:1fr}}
</style>
<?php
$styles = ob_get_clean();
renderHeader('Panel de administración', $styles);
renderBanner('Panel de administración');
?>
<div class="container">
  <h1>Panel de administración</h1>

  <section class="kpis">
    <div class="kpi"><div id="kpiBiz" class="val">0</div><div class="lab">Barberías activas</div></div>
    <div class="kpi"><div id="kpiStaff" class="val">0</div><div class="lab">Barberos activos</div></div>
    <div class="kpi"><div id="kpiAvg" class="val">0.0</div><div class="lab">Citas/día (promedio 7d)</div></div>
    <div class="kpi"><div id="kpiCancel" class="val">0%</div><div class="lab">Tasa de cancelación</div></div>
  </section>

  <section class="box">
    <h2 class="subttl">Resumen de comentarios</h2>

    <div class="wrap-flex">
      <div style="flex:1 1 480px;min-width:280px">
        <div class="legend">Promedio</div>
        <div style="font-size:28px;font-weight:700"><span id="avgScore">0.0</span> / 5.0 ★</div>

        <div class="dist">
          <div>5★<div class="bar"><i id="b5"></i></div><div class="legend" id="p5">0%</div></div>
          <div>4★<div class="bar"><i id="b4"></i></div><div class="legend" id="p4">0%</div></div>
          <div>3★<div class="bar"><i id="b3"></i></div><div class="legend" id="p3">0%</div></div>
          <div>2★<div class="bar"><i id="b2"></i></div><div class="legend" id="p2">0%</div></div>
          <div>1★<div class="bar"><i id="b1"></i></div><div class="legend" id="p1">0%</div></div>
        </div>
      </div>

      <div style="flex:0 0 260px">
        <div class="chart"><canvas id="pie" width="220" height="220"></canvas></div>
        <div class="pie-legend">
          <div><span class="dot dot-green"></span> <strong>Sí:</strong> <span id="yesPct">0%</span></div>
          <div style="margin-top:4px"><span class="dot dot-red"></span> <strong>No:</strong> <span id="noPct">0%</span></div>
        </div>
      </div>
    </div>

    <div class="filters">
      <label>Periodo: <select id="selPeriod"><option>Últimos 30 días</option><option>Últimos 90 días</option></select></label>
      <label class="destino">Destino: <select id="selScope"><option>Página</option><option>Negocios</option></select></label>
    </div>

    <div id="comments"></div>
  </section>

  <section class="box">
    <h2 class="subttl">Tokens</h2>
    <div class="wrap-flex" style="gap:12px;">
      <div class="kpi" style="flex:1 1 220px;">
        <div class="lab">Comprados este mes</div>
        <div id="tokensBought" class="val">0</div>
      </div>
      <div class="kpi" style="flex:1 1 220px;">
        <div class="lab">Consumidos este mes</div>
        <div id="tokensSpent" class="val">0</div>
      </div>
      <div class="kpi" style="flex:1 1 220px;">
        <div class="lab">Saldo total</div>
        <div id="tokensBalance" class="val">0</div>
      </div>
    </div>
  </section>

  <section class="box">
    <h2 class="subttl">Acciones</h2>
    <button id="btnSnapshot" class="btn">Generar snapshot</button>
  </section>
</div>

<script>
  const stats = {
    negociosActivos: 124,
    barberosActivos: 412,
    promCitas7d: 56.4,
    tasaCancelacion: 8.2,
    promedio: 4.3,
    distribucion: { 5: 42, 4: 31, 3: 15, 2: 8, 1: 4 },
    recomiendanSi: 76,
    recomiendanNo: 24,
    comentarios: [
      { autor:'Ana María', estrellas:5, sentimiento:'pos', texto:'Excelente servicio y puntualidad.', fecha:'2025-02-10' },
      { autor:'Carlos', estrellas:3, sentimiento:'neu', texto:'Buen servicio aunque pueden mejorar los horarios.', fecha:'2025-02-08' },
      { autor:'Luisa', estrellas:1, sentimiento:'neg', texto:'Tuve un inconveniente con la reserva.', fecha:'2025-02-05' }
    ],
    tokens: { comprados: 8200, consumidos: 7640, saldo: 4100 }
  };

  document.getElementById('kpiBiz').textContent = stats.negociosActivos;
  document.getElementById('kpiStaff').textContent = stats.barberosActivos;
  document.getElementById('kpiAvg').textContent = stats.promCitas7d.toFixed(1);
  document.getElementById('kpiCancel').textContent = stats.tasaCancelacion.toFixed(1) + '%';

  document.getElementById('avgScore').textContent = stats.promedio.toFixed(1);
  const totalComentarios = Object.values(stats.distribucion).reduce((acc, val) => acc + val, 0) || 1;
  for (let i = 5; i >= 1; i--) {
    const porcentaje = Math.round((stats.distribucion[i] / totalComentarios) * 100);
    document.getElementById('b' + i).style.width = porcentaje + '%';
    document.getElementById('p' + i).textContent = porcentaje + '%';
  }

  const totalRecomiendan = stats.recomiendanSi + stats.recomiendanNo || 1;
  document.getElementById('yesPct').textContent = Math.round((stats.recomiendanSi / totalRecomiendan) * 100) + '%';
  document.getElementById('noPct').textContent = Math.round((stats.recomiendanNo / totalRecomiendan) * 100) + '%';

  const commentsContainer = document.getElementById('comments');
  commentsContainer.innerHTML = stats.comentarios.map(c => `
    <article class="cmt">
      <div>
        <div class="cmt-head">
          <strong>${c.autor}</strong>
          <span class="stars">${'★'.repeat(c.estrellas)}${'☆'.repeat(5 - c.estrellas)}</span>
          <span class="tag ${c.sentimiento}">${c.sentimiento === 'pos' ? 'Positivo' : c.sentimiento === 'neg' ? 'Negativo' : 'Neutro'}</span>
        </div>
        <p style="margin:6px 0 0;">${c.texto}</p>
      </div>
      <div class="muted">${new Date(c.fecha + 'T00:00').toLocaleDateString('es-CO', { timeZone:'America/Bogota' })}</div>
    </article>
  `).join('');

  document.getElementById('tokensBought').textContent = stats.tokens.comprados.toLocaleString('es-CO');
  document.getElementById('tokensSpent').textContent = stats.tokens.consumidos.toLocaleString('es-CO');
  document.getElementById('tokensBalance').textContent = stats.tokens.saldo.toLocaleString('es-CO');

  document.getElementById('btnSnapshot').addEventListener('click', () => {
    alert('Generar snapshot (modo demo).');
  });
</script>
<?php renderLegal(); ?>
<?php renderFooter(); ?>
