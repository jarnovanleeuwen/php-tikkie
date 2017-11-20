<?php
namespace PHPTikkie;

use PHPTikkie\Exceptions\ResponseException;
use Psr\Http\Message\ResponseInterface;

class Response
{
    /**
     * @var string
     */
    const TRACE_ID_HEADER = 'Trace-Id';

    /**
     * @var string
     */
    private $traceId;

    /**
     * @var array
     */
    private $data;

    public function __construct(ResponseInterface $response)
    {
        if (!$response->hasHeader(static::TRACE_ID_HEADER)) {
            throw new ResponseException("Trace ID header missing");
        }

        $this->traceId = $response->getHeader(static::TRACE_ID_HEADER);

        if (($this->data = json_decode($response->getBody(), true)) === null) {
            throw new ResponseException("Could not decode response");
        }
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }
}
