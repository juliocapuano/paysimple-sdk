<?php

namespace PaySimple\V4\Domain\ValueObject;

use PaySimple\V4\Domain\Exception\EntityException;

final class Email
{
    private string $email;

    private function __construct(string $email)
    {
        $this->validate($email);
        $this->email = strtolower(trim($email));
    }

    private function validate(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw EntityException::invalidProperty('Email', $email);
        }
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    public function toString(): string
    {
        return $this->email;
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
