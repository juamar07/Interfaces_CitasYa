<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    public static function render(string $view, array $data = []): void
    {
        $viewPath = self::resolvePath($view);
        if (!is_file($viewPath)) {
            throw new RuntimeException(sprintf('View %s not found.', $view));
        }

        extract($data, EXTR_SKIP);
        include $viewPath;
    }

    private static function resolvePath(string $view): string
    {
        $view = str_replace(['..', '\\'], '', $view);
        if (!str_ends_with($view, '.php')) {
            $view .= '.php';
        }

        return __DIR__ . '/../views/' . $view;
    }
}
