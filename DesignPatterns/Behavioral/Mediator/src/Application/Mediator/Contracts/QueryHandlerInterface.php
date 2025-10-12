<?php
declare(strict_types=1);

namespace App\Application\Mediator\Contracts;

interface QueryHandlerInterface
{
    /** @return mixed */
    public function handle(Query $query);
}
