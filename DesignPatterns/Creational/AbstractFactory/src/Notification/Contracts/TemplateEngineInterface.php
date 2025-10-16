<?php
declare(strict_types=1);

namespace App\Notification\Contracts;

interface TemplateEngineInterface
{
    public function render(string $template, array $data = []): string;
}
