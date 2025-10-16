<?php
declare(strict_types=1);

namespace Tests\US;

use App\Notification\US\UsTemplateEngine;
use PHPUnit\Framework\TestCase;

final class UsTemplateEngineTest extends TestCase
{
    public function testRenderEscapesAndHasUsFooter(): void
    {
        $tpl = new UsTemplateEngine();
        $html = $tpl->render('Hello {{name}}', ['name' => '<b>Ann</b>']);

        $this->assertStringContainsString('Hello &lt;b&gt;Ann&lt;/b&gt;', $html);
        $this->assertStringContainsString('Data stored in the US.', $html);
    }
}
