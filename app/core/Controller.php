<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $path, array $data = []): void
    {
        View::render($path, $data);
    }

    protected function redirect(string $path): void
    {
        Helpers::redirect($path);
    }
}
