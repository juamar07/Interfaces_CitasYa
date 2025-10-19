<?php
declare(strict_types=1);

use App\Core\Flash;
use App\Core\Helpers;

if (!function_exists('renderHeader')) {
    /**
     * @param string $title
     * @param string $extraHead
     */
    function renderHeader(string $title, string $extraHead = ''): void
    {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helpers::e($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <?= $extraHead ?>
</head>
<body>
<?php
        renderFlash();
    }
}

if (!function_exists('renderFlash')) {
    function renderFlash(): void
    {
        $messages = Flash::consume();
        if (empty($messages)) {
            return;
        }
        ?>
    <div class="flash-wrapper" style="max-width: 840px; margin: 12px auto;">
        <?php foreach ($messages as $type => $group): ?>
            <?php foreach ($group as $message): ?>
                <div class="flash-message flash-<?= Helpers::e($type) ?>" style="background:#f5f5f5;border-left:4px solid #5c6bc0;padding:12px 16px;margin-bottom:8px;border-radius:6px;color:#233247;">
                    <?= Helpers::e($message) ?>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php
    }
}
