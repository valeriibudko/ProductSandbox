<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap\Container;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle7\Client as Psr18Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$guzzle = new GuzzleClient();
$http = new Psr18Client($guzzle);

$logger = new Logger('notify');
$logger->pushHandler(new StreamHandler('php://stdout'));

$region = $argv[1] ?? 'EU';

$notifier = Container::build($region, $http, $logger);
$notifier->notifyUser('user@can.net', '+4912345678', 'Viktoria');

echo "OK\n";
