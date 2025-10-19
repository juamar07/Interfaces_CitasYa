<?php
declare(strict_types=1);

use App\Core\Helpers;

if (!function_exists('renderBanner')) {
    function renderBanner(string $title, ?string $backUrl = null, string $homeUrl = '/', string $backLabel = '&larr; Volver'): void
    {
        $backHref = $backUrl ?? 'javascript:history.back()';
        ?>
<header class="app-banner" role="banner">
    <div class="banner-box">
        <div class="banner-inner">
            <a href="<?= Helpers::e($backHref) ?>" class="back-button banner-back"><?= $backLabel ?></a>
            <div class="banner-title"><?= Helpers::e($title) ?></div>
            <a href="<?= Helpers::e($homeUrl) ?>" class="banner-logo" aria-label="Ir al inicio">
                <img src="/assets/img/LogoCitasYa.png" alt="Citas Ya">
            </a>
        </div>
    </div>
</header>
<?php
    }
}
