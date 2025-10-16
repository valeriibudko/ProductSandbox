<?php
declare(strict_types=1);

namespace App\Notification\Contracts;

interface SmsSenderInterface
{
    public function send(string $phone, string $text): void;
}
