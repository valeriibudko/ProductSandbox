<?php
declare(strict_types=1);

namespace Tests\Doubles;

use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class MockHttpClient implements ClientInterface
{
    /** @var RequestInterface[] */
    public array $requests = [];

    /** @var callable(RequestInterface):ResponseInterface|null */
    private $responder;

    private ResponseInterface $defaultResponse;

    public function __construct(?callable $responder = null, ?ResponseInterface $defaultResponse = null)
    {
        $this->responder = $responder;
        $this->defaultResponse = $defaultResponse ?? new Response(200, ['Content-Type' => 'application/json'], '{}');
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;
        if ($this->responder) {
            return ($this->responder)($request);
        }
        return $this->defaultResponse;
    }
}
