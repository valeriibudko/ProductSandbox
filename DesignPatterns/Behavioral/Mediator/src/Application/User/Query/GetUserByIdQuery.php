<?php

declare(strict_types=1);

namespace App\Application\User\Query;

use App\Application\Mediator\Contracts\Query;

final class GetUserByIdQuery implements Query
{
    public function __construct(public int $id)
    {
    }
}
