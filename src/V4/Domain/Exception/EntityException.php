<?php

namespace PaySimple\V4\Domain\Exception;

use RuntimeException;

class EntityException extends RuntimeException
{
    public static function invalidProperty(string $property, string $value): self
    {
        return new self(sprintf('Invalid value "%s" for property "%s"', $value, $property));
    }

    public static function missingRequiredProperty(string $property): self
    {
        return new self(sprintf('Required property "%s" is missing', $property));
    }
}
