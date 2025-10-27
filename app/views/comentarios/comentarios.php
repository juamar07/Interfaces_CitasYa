<?php
declare(strict_types=1);

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/banner.php';
require __DIR__ . '/../../includes/legal.php';
require __DIR__ . '/../../includes/footer.php';

ob_start();
?>
<style>
  :root{
    --container-w: 900px;
    --container-pad: 20px;
    --container-bl: 4px;

    --banner-h: 64px;
    --page-sidepad: 16px;
    --banner-bg: #e6e9ee;
    --banner-bg-hover: #d7dbe3;

    --brand-blue:#5c6bc0;
    --green:#66BB6A; --green-h:#57A05D;
    --red:#f44336;   --red-h:#d32f2f;
  }
  body{
    font-family:'Open Sans', sans-serif;
    background:#eee; color:#333; margin:0; padding:20px;
    padding-top: calc(var(--banner-h) + 8px);
  }
  .container{
    max-width: var(--container-w); margin:auto;
    background:#fff; border-radius:10px; padding:var(--container-pad);
    box-shadow:0 4px 8px rgba(0,0,0,.05);
    border-left: var(--container-bl) solid var(--brand-blue);
  }

  .app-banner{ position:fixed; top:0; left:0; right:0; height:var(--banner-h); z-index:9999; }
  .app-banner .banner-box{
    height:100%;
    width:min(calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl)),
              calc(100% - var(--page-sidepad)*2));
    margin:0 auto; background:var(--banner-bg);
    border-bottom:1px solid rgba(0,0,0,.06); border-radius:10px;
    display:flex; align-items:center; transition:background-color .2s ease;
  }
  .app-banner .banner-box:hover{ background:var(--banner-bg-hover); }
  .banner-inner{ display:grid; grid-template-columns:1fr auto 1fr; gap:8px; width:100%; padding:0 12px; align-items:center; }
  .banner-title{ justify-self:center; font-weight:700; color:#233247; }
  .banner-logo{ justify-self:end; display:inline-flex; align-items:center; }
  .banner-logo img{ width:52px; height:auto; }
  .back-button{
    display:inline-block; text-decoration:none; font-size:14px; color:var(--brand-blue);
    padding:8px 12px; border:1px solid var(--brand-blue); border-radius:6px;
    transition:.2s; justify-self:start;
  }
  .back-button:hover{ background:var(--brand-blue); color:#fff; }

  h1{ text-align:center; margin:8px 0 18px; }

  label{ display:block; margin:10px 0 6px; color:#003366; }
  input[type="text"], select, textarea{
    width:100%; padding:12px; border:2px solid #ddd; border-radius:6px; font-size:16px;
    box-sizing:border-box; transition:border-color .2s;
  }
  textarea{ min-height:120px; resize:vertical; }
  input:focus, select:focus, textarea:focus{ outline:none; border-color:#7da2a9; }

  .row-inline{ display:flex; gap:14px; align-items:center; flex-wrap:wrap; }
  .row-inline .grow{ flex:1 1 auto; }

  .stars{ display:flex; gap:8px; font-size:28px; user-select:none; }
  .star{
    width:34px; height:34px; cursor:pointer; display:inline-flex; align-items:center; justify-content:center;
    border-radius:6px; border:1px solid transparent;
  }
  .star:hover{ transform:translateY(-1px); }
  .star.is-on{ color:#f5a623; }
  .star.is-off{ color:#c7c7c7; }

  .btn-main{
    display:block; width:min(680px, 75%); margin:18px auto 6px;
    padding:14px 20px; border:none; border-radius:8px; color:#fff; background:var(--green);
    font-weight:700; font-size:16px; cursor:pointer; transition:.2s;
  }
  .btn-main:hover{ background:var(--green-h); transform:translateY(-2px); }

  .hint{ color:#666; font-size:13px; margin-top:4px; }
  .badge{ display:inline-block; padding:3px 8px; border-radius:20px; font-size:12px; margin-left:8px; }
  .badge.neg{ background:#ffe4e4; color:#b00020; }
  .badge.neu{ background:#eef1f6; color:#445; }
  .badge.pos{ background:#e9f7ef; color:#1b5e20; }

  .msg{ text-align:center; font-size:14px; margin-top:8px; }
  .msg.ok{ color:#1b5e20; }
  .msg.err{ color:#b00020; }

  .legal-outside{
    margin:18px auto 24px; padding:10px 12px;
    max-width: calc(var(--container-w) + var(--container-pad)*2 + var(--container-bl));
    text-align:center; color:#666; font-size:14px; line-height:1.35;
  }
</style>
<?php
$styles = ob_get_clean();
renderHeader('Comentarios', $styles);
renderBanner('Comentarios');
?>
<div class="container">
  <h1>Cuéntanos tu experiencia</h1>

  <label for="inpName">Tu nombre <span class="hint">(opcional)</span></label>
  <input id="inpName" type="text" placeholder="Ej: Juan Pérez" />

  <label for="selTarget">Selecciona a quién va dirigido</label>
  <select id="selTarget">
    <option value="pagina">A la página</option>
    <option value="barberia">A una barbería</option>
  </select>

  <div id="targetBizWrap" style="display:none;">
    <label for="inpBiz">Escribe el nombre de la barbería</label>
    <input id="inpBiz" list="bizList" type="text" placeholder="Ej: Barbería Central" autocomplete="off"/>
    <datalist id="bizList"></datalist>
    <div class="hint">Se sugiere con base en barberías registradas.</div>
  </div>

  <label for="txtComment">Escribe tu comentario</label>
  <textarea id="txtComment" placeholder="¿Qué te gustó o qué podemos mejorar?"></textarea>

  <div class="row-inline" style="margin-top:10px;">
    <div class="grow">
      <label>Déjanos tu calificación</label>
      <div id="starCtrl" class="stars" aria-label="Calificación de 1 a 5">
        <span class="star" data-v="1">★</span>
        <span class="star" data-v="2">★</span>
        <span class="star" data-v="3">★</span>
        <span class="star" data-v="4">★</span>
        <span class="star" data-v="5">★</span>
      </div>
    </div>
    <div>
      <label>¿Recomiendas este servicio?</label>
      <div class="row-inline">
        <label><input type="radio" name="recommend" value="si" checked> Sí</label>
        <label><input type="radio" name="recommend" value="no"> No</label>
      </div>
    </div>
  </div>

  <button id="btnSend" class="btn-main">Enviar comentario</button>
  <p id="msg" class="msg" aria-live="polite"></p>

  <section style="margin-top:24px;">
    <h2 style="margin-bottom:12px;">Comentarios recientes</h2>
    <div id="commentsList"></div>
  </section>
</div>

<template id="tplComment">
  <article class="comment-card" style="border:1px solid #dce3f0; border-radius:10px; padding:14px; margin-bottom:12px; background:#f8f9fd;">
    <header style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <strong>{{autor}}</strong>
        <span class="badge {{sentClass}}">{{sentLabel}}</span>
      </div>
      <div style="font-weight:700; color:#f5a623;">{{stars}}</div>
    </header>
    <p style="margin:8px 0;">{{texto}}</p>
    <footer style="display:flex; justify-content:space-between; font-size:13px; color:#546e7a;">
      <span>{{destino}}</span>
      <span>{{fecha}}</span>
    </footer>
  </article>
</template>

<script>
  const negocios = ['Barbería Central','Barbería Medellín','Caballeros Elite'];
  const comentarios = [
    { autor:'Ana María', calificacion:5, recomienda:true, sentimiento:'positivo', texto:'Servicio excelente, los barberos muy profesionales.', destino:'Barbería Central', fecha:'2025-02-10' },
    { autor:'Juan Camilo', calificacion:3, recomienda:true, sentimiento:'neutro', texto:'Buen servicio pero podrían mejorar los tiempos de espera.', destino:'Barbería Medellín', fecha:'2025-02-09' },
    { autor:'Sofía', calificacion:1, recomienda:false, sentimiento:'negativo', texto:'No me gustó la atención, tardaron demasiado.', destino:'Página', fecha:'2025-02-05' }
  ];

  const starCtrl = document.getElementById('starCtrl');
  const msg = document.getElementById('msg');
  const bizList = document.getElementById('bizList');
  const targetWrap = document.getElementById('targetBizWrap');
  const selTarget = document.getElementById('selTarget');
  const commentsList = document.getElementById('commentsList');
  const tpl = document.getElementById('tplComment').innerHTML;

  bizList.innerHTML = negocios.map(n => `<option value="${n}"></option>`).join('');

  let currentRating = 5;
  function updateStars(rating) {
    currentRating = rating;
    starCtrl.querySelectorAll('.star').forEach(star => {
      const value = parseInt(star.dataset.v, 10);
      star.classList.toggle('is-on', value <= rating);
      star.classList.toggle('is-off', value > rating);
    });
  }

  starCtrl.addEventListener('click', event => {
    const target = event.target;
    if (target.classList.contains('star')) {
      updateStars(parseInt(target.dataset.v, 10));
    }
  });

  updateStars(currentRating);

  selTarget.addEventListener('change', () => {
    if (selTarget.value === 'barberia') {
      targetWrap.style.display = 'block';
    } else {
      targetWrap.style.display = 'none';
    }
  });

  function sentimentClass(sent) {
    if (sent === 'positivo') return 'pos';
    if (sent === 'negativo') return 'neg';
    return 'neu';
  }

  function renderComentarios() {
    commentsList.innerHTML = comentarios.map(c => tpl
      .replace('{{autor}}', c.autor || 'Anónimo')
      .replace('{{sentClass}}', sentimentClass(c.sentimiento))
      .replace('{{sentLabel}}', c.sentimiento.charAt(0).toUpperCase() + c.sentimiento.slice(1))
      .replace('{{stars}}', '★'.repeat(c.calificacion) + '☆'.repeat(5 - c.calificacion))
      .replace('{{texto}}', c.texto)
      .replace('{{destino}}', c.destino)
      .replace('{{fecha}}', new Date(c.fecha + 'T00:00').toLocaleDateString('es-CO', { timeZone:'America/Bogota' }))
    ).join('');
  }

  renderComentarios();

  document.getElementById('btnSend').addEventListener('click', () => {
    const nombre = document.getElementById('inpName').value.trim();
    const comentario = document.getElementById('txtComment').value.trim();
    const destino = selTarget.value === 'barberia' ? document.getElementById('inpBiz').value.trim() : 'Página';
    const recomienda = document.querySelector('input[name="recommend"]:checked').value === 'si';

    if (!comentario) {
      msg.textContent = 'Por favor escribe un comentario antes de enviar.';
      msg.className = 'msg err';
      return;
    }

    comentarios.unshift({
      autor: nombre || 'Anónimo',
      calificacion: currentRating,
      recomienda,
      sentimiento: currentRating >= 4 ? 'positivo' : currentRating >= 3 ? 'neutro' : 'negativo',
      texto: comentario,
      destino: destino || 'Página',
      fecha: new Date().toISOString().slice(0,10)
    });

    renderComentarios();
    msg.textContent = '¡Gracias por compartir tu experiencia!';
    msg.className = 'msg ok';
    document.getElementById('txtComment').value = '';
  });
</script>
<?php renderLegal(); ?>
<?php renderFooter(); ?>
