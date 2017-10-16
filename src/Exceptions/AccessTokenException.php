<?php
namespace PHPTikkie\Exceptions;

use Exception;
use RuntimeException;

class AccessTokenException extends RuntimeException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $message = "Could not create access token: {$message}";

        parent::__construct($message, $code, $previous);
    }
}
