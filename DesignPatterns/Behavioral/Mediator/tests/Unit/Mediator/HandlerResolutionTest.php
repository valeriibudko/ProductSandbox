<?php
declare(strict_types=1);

namespace Tests\Mediator;

use PHPUnit\Framework\TestCase;

use App\Application\Mediator\SimpleContainer;
use App\Application\Mediator\Mediator;
use App\Application\Mediator\Contracts\Command;

final class HandlerResolutionTest extends TestCase
{
    public function testNoHandlerThrows(): void
    {
        $c = new SimpleContainer();
        $handlers = [];        // нет маппинга
        $middleware = [];      // неважно

        $mediator = new Mediator($c, $handlers, $middleware);

        $this->expectException(\RuntimeException::class);
        $mediator->send(new class implements Command {});
    }
}
