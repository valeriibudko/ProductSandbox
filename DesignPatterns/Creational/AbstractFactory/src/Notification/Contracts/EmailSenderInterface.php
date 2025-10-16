<?php
declare(strict_types=1);

namespace App\Notification\Contracts;

interface EmailSenderInterface
{
    public function send(string $to, string $subject, string $html): void;
}
