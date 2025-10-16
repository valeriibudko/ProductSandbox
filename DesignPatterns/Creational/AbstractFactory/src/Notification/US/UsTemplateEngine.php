<?php
declare(strict_types=1);

namespace App\Notification\US;

use App\Notification\Contracts\TemplateEngineInterface;

final class UsTemplateEngine implements TemplateEngineInterface
{
    public function render(string $template, array $data = []): string
    {
        $out = $template;
        foreach ($data as $k => $v) {
            $out = str_replace('{{' . $k . '}}', htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), $out);
        }
        return $out . '<br><small>Data stored in the US.</small>';
    }
}
