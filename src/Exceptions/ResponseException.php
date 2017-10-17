<?php
namespace PHPTikkie\Exceptions;

use Exception;

class RequestException extends PHPTikkieException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
