<?php
declare(strict_types=1);

namespace Tests\EU;

use App\Notification\EU\EuTemplateEngine;
use PHPUnit\Framework\TestCase;

final class EuTemplateEngineTest extends TestCase
{
    public function testRenderEscapesAndHasEuFooter(): void
    {
        $tpl = new EuTemplateEngine();
        $html = $tpl->render('Hello {{name}}', ['name' => '<b>Ann</b>']);

        $this->assertStringContainsString('Hello &lt;b&gt;Ann&lt;/b&gt;', $html);
        $this->assertStringContainsString('Data stored in the EU.', $html);
    }
}
