<?php

namespace PHPTikkie\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PHPTikkie\Environment;
use PHPTikkie\PHPTikkie;
use Psr\Http\Message\ResponseInterface;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    const API_KEY = 'abcdefghijklmnop';
    const MOCK_PATH = __DIR__.'/Mock';

    /**
     * History of requests and responses.
     *
     * @var array
     */
    protected $history;

    /**
     * @var PHPTikkie
     */
    protected $tikkie;

    public function setUp()
    {
        $this->history = [];
        $this->tikkie = $this->createPHPTikkie();
    }

    protected function createPHPTikkie(array $options = []): PHPTikkie
    {
        $environment = new Environment(static::API_KEY, false, $options);
        $environment->loadPrivateKey(static::MOCK_PATH.'/private_rsa.pem');

        return new PHPTikkie($environment);
    }

    protected function getMockHttpResponse(string $path): ResponseInterface
    {
        return \GuzzleHttp\Psr7\parse_response(file_get_contents(static::MOCK_PATH.'/'.$path));
    }

    /**
     * @param string|array $paths
     */
    protected function setMockHttpResponse($paths): void
    {
        $history = Middleware::history($this->history);

        $responses = [];
        foreach ((array) $paths as $path) {
            $responses[] = $this->getMockHttpResponse($path);
        }

        $mock = new MockHandler($responses);

        $stack = HandlerStack::create($mock);
        $stack->push($history);

        $this->tikkie = $this->createPHPTikkie([
            'handler' => $stack
        ]);
    }
}
