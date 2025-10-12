<?php
declare(strict_types=1);

namespace App\Application\Mediator;

use App\Application\Mediator\Contracts\MediatorInterface;
use App\Application\Mediator\Contracts\Command;
use App\Application\Mediator\Contracts\Query;
use App\Application\Mediator\Contracts\CommandHandlerInterface;
use App\Application\Mediator\Contracts\QueryHandlerInterface;
use App\Application\Mediator\Contracts\MiddlewareInterface;

final class Mediator implements MediatorInterface
{
    /** @param array<class-string, class-string> $handlers */
    public function __construct(
        private SimpleContainer $container,
        private array $handlers,
        /** @var MiddlewareInterface[] */
        private array $middleware = []
    ) {}

    public function send(Command|Query $message): mixed
    {
        $messageClass = $message::class;
        $handlerClass = $this->handlers[$messageClass] ?? null;

        if (!$handlerClass) {
            throw new \RuntimeException("Handler not found for {$messageClass}");
        }

        $core = function (object $msg) use ($handlerClass) {
            $handler = $this->container->get($handlerClass);

            if ($msg instanceof Command && $handler instanceof CommandHandlerInterface) {
                return $handler->handle($msg);
            }
            if ($msg instanceof Query && $handler instanceof QueryHandlerInterface) {
                return $handler->handle($msg);
            }
            throw new \RuntimeException("Handler {$handlerClass} does not support ". $msg::class);
        };

        // Wrap the core with a chain of middleware. The last one added is external
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            fn (callable $next, MiddlewareInterface $mw) =>
            fn (object $msg) => $mw->process($msg, $next),
            $core
        );

        return $pipeline($message);
    }
}
