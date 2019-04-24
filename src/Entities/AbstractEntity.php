<?php
namespace PHPTikkie\Entities;

use DateTimeImmutable;
use PHPTikkie\PHPTikkie;

abstract class AbstractEntity
{
    /**
     * @var PHPTikkie
     */
    private $tikkie;

    /**
     * @var array
     */
    protected $fillableAttributes = [];

    public function __construct(PHPTikkie $tikkie)
    {
        $this->tikkie = $tikkie;
    }

    protected function getTikkie(): PHPTikkie
    {
        return $this->tikkie;
    }

    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillableAttributes)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Convert an ISO-8601 formatted string to DateTimeImmutable.
     */
    protected function toDateTime(string $representation): DateTimeImmutable
    {
        // Due to a Tikkie bug, the API may return epoch timestamps with milliseconds instead of a ISO-8601 formatted string.
        // I reported this on 24-04-2019.
        if (is_numeric($representation)) {
            // Remove milliseconds and prepend with @ to mark as timestamp.
            $representation = '@'.substr($representation, 0, 10);
        }

        return new DateTimeImmutable($representation);
    }
}
