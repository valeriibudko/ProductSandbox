<?php
declare(strict_types=1);

namespace App\Application\Mediator\Contracts;

interface CommandHandlerInterface
{
    /** @return mixed */
    public function handle(Command $command);
}
