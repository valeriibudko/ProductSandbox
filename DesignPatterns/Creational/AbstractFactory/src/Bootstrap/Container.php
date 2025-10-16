<?php
declare(strict_types=1);

namespace App\Bootstrap;

use App\Notification\WelcomeNotifier;
use App\Notification\EU\EuNotificationFactory;
use App\Notification\US\UsNotificationFactory;
use App\Notification\Infra\HttpGateway;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

final class Container
{
    public static function build(string $region, ClientInterface $http, LoggerInterface $logger): WelcomeNotifier
    {

        $psr17 = new Psr17Factory();
        $mailEndpoint = getenv('MAIL_ENDPOINT') ?: 'http://127.0.0.1:8080';
        $smsEndpoint  = getenv('SMS_ENDPOINT')  ?: 'http://127.0.0.1:8080';

        $mailGw = new HttpGateway($http, $psr17, $mailEndpoint);
        $smsGw  = new HttpGateway($http, $psr17, $smsEndpoint);

        $factory = match (strtoupper($region)) {
            'EU' => new EuNotificationFactory($mailGw, $smsGw, $logger),
            default => new UsNotificationFactory($mailGw, $smsGw, $logger),
        };

        return new WelcomeNotifier($factory);
    }
}
