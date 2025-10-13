<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Enum\Channel;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ChannelTest extends TestCase
{
    public function testFromStringParsesKnownValues(): void
    {
        $this->assertSame(Channel::EMAIL, Channel::fromString('email'));
        $this->assertSame(Channel::SMS, Channel::fromString('sms'));
        $this->assertSame(Channel::EMAIL, Channel::fromString('EmAiL')); // case not sensitive
    }

    public function testFromStringThrowsOnUnknown(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Channel::fromString('push');
    }
}
