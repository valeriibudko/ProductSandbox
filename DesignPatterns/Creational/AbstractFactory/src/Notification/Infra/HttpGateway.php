<?php
declare(strict_types=1);

namespace App\Notification\Infra;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Nyholm\Psr7\Stream;

final class HttpGateway
{
    public function __construct(
        private ClientInterface $http,
        private RequestFactoryInterface $reqFactory,
        private string $endpoint
    ) {}

    public function postJson(string $path, array $payload): void
    {
        $req = $this->reqFactory->createRequest('POST', rtrim($this->endpoint, '/') . $path)
            ->withHeader('Content-Type', 'application/json');

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        rewind($stream);

        $req = $req->withBody(Stream::create($stream));
        $this->http->sendRequest($req);
    }
}
