<?php
namespace PHPTikkie\Entities;

use DomainException;
use PHPTikkie\PHPTikkie;

class AbstractEntity
{
    /**
     * @var PHPTikkie
     */
    private $tikkie;

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
            if (property_exists(static::class, $key)) {
                $this->{$key} = $value;
            } else {
                throw new DomainException("Unknown property [{$key}]");
            }
        }
    }
}
