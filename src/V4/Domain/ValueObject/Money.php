<?php

namespace PaySimple\V4\Domain\ValueObject;

use PaySimple\V4\Domain\Exception\EntityException;

final class Money
{
    private int $amount;

    private function __construct(int $amount)
    {
        $this->validate($amount);
        $this->amount = $amount;
    }

    private function validate(int $amount): void
    {
        if ($amount < 0) {
            throw EntityException::invalidProperty('Amount', (string) $amount);
        }
    }

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    public static function fromDollars(float $dollars): self
    {
        return new self((int) round($dollars * 100));
    }

    public function toCents(): int
    {
        return $this->amount;
    }

    public function toDollars(): float
    {
        return $this->amount / 100;
    }

    public function toString(): string
    {
        return number_format($this->toDollars(), 2, '.', '');
    }

    public function add(self $money): self
    {
        return new self($this->amount + $money->amount);
    }

    public function subtract(self $money): self
    {
        $result = $this->amount - $money->amount;
        if ($result < 0) {
            throw EntityException::invalidProperty('Money result', (string) $result);
        }
        return new self($result);
    }

    public function equals(self $money): bool
    {
        return $this->amount === $money->amount;
    }

    public function isGreaterThan(self $money): bool
    {
        return $this->amount > $money->amount;
    }

    public function isLessThan(self $money): bool
    {
        return $this->amount < $money->amount;
    }
}
