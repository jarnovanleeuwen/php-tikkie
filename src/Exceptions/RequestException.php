<?php
namespace PHPTikkie\Exceptions;

use Exception;
use RuntimeException;

class RequestException extends RuntimeException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $message = "Request to Tikkie API endpoint failed: {$message}";

        parent::__construct($message, $code, $previous);
    }
}
