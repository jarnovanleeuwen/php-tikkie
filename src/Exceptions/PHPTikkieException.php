<?php
namespace PHPTikkie\Exceptions;

use Exception;
use RuntimeException;

class PHPTikkieException extends RuntimeException
{
    /**
     * @var array
     */
    private $errors = [];

    public function __construct($message, $code = 0, Exception $previous = null)
    {
        // Try to decode into a Tikkie API error response.
        $response = json_decode($message);

        if ($response !== null) {
            $this->errors = $response->errors ?? [];

            if (count($this->errors) > 0) {
                $error = $this->errors[0];

                $message = "[{$error->code}] {$error->message} (see {$error->reference})";
            }
        }

        parent::__construct($message, $code, $previous);
    }
}
