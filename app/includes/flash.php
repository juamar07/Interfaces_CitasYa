<?php
declare(strict_types=1);

use App\Core\Flash;
use App\Core\Helpers;

$messages = Flash::consume();
if (!empty($messages)):
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
<?php endif; ?>
