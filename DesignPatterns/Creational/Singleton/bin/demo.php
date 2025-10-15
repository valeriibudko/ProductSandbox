<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Service\Logger;

$loggerOne = Logger::getInstance();
$loggerTwo = Logger::getInstance();

if ($loggerOne === $loggerTwo) {
    $loggerOne->info('Application started with single instance');
    $loggerOne->warning('Low memory warning', ['free' => '120MB']);
    $loggerOne->error('Database connection failed', ['host' => 'localhost']);

} else {
    $loggerOne->error('Loggers are different.');
}


