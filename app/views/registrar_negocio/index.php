<?php
declare(strict_types=1);

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/banner.php';
require __DIR__ . '/../../includes/legal.php';
require __DIR__ . '/../../includes/footer.php';

ob_start();
?>
<style>
    body{font-family:'Open Sans',sans-serif;background:#eee;margin:0;padding:20px;padding-top:72px;color:#333;}
    .container{max-width:800px;margin:auto;padding:20px;background:#fff;border-radius:10px;box-shadow:0 4px 8px rgba(0,0,0,.05);border-left:4px solid #5c6bc0;}
    .app-banner{position:fixed;top:0;left:0;right:0;height:64px;z-index:9999;background:transparent;}
    .app-banner .banner-box{height:100%;width:min(100%, calc(800px + 40px + 4px));margin:0 auto;background:#e6e9ee;border-bottom:1px solid rgba(0,0,0,.06);border-radius:10px;display:flex;align-items:center;}
    .app-banner .banner-inner{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:8px;width:100%;padding:0 12px;}
    .banner-title{justify-self:center;font-weight:700;color:#233247;}
    .back-button{justify-self:start;text-decoration:none;color:#5c6bc0;padding:8px 12px;border:1px solid #5c6bc0;border-radius:6px;}
    .back-button:hover{background:#5c6bc0;color:#fff;}
    .banner-logo{justify-self=end;display:inline-flex;align-items:center;}
    .banner-logo img{width:52px;height:auto;display:block;}
    h1{text-align:center;}
    .legal-outside{margin:18px auto 24px;padding:10px 12px;max-width:calc(800px + 40px + 4px);text-align:center;color:#666;font-size:14px;line-height:1.35;}
</style>
<?php
$styles = ob_get_clean();
renderHeader('Registrar negocio', $styles);
renderBanner('Registrar negocio');
?>
<div class="container">
    <h1>Registrar negocio</h1>
    <p style="text-align:center;">Vista en construcción.</p>
</div>
<?php renderLegal(); ?>
<?php renderFooter(); ?>
