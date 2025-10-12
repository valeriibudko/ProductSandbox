<?php
declare(strict_types=1);

namespace App\Application\Mediator\Contracts;

interface MediatorInterface
{
    /** @return mixed */
    public function send(Command|Query $message);
}
