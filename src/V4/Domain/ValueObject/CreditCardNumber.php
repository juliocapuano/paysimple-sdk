<?php

namespace PaySimple\V4\Domain\ValueObject;

use PaySimple\V4\Domain\Exception\EntityException;

final class CreditCardNumber
{
    private string $number;

    private function __construct(string $number)
    {
        $this->validate($number);
        $this->number = $number;
    }

    private function validate(string $number): void
    {
        $cleaned = preg_replace('/\s+/', '', $number);
        
        if (!preg_match('/^\d{13,19}$/', $cleaned)) {
            throw EntityException::invalidProperty('CreditCardNumber', $number);
        }

        if (!$this->luhnCheck($cleaned)) {
            throw EntityException::invalidProperty('CreditCardNumber', $number);
        }
    }

    private function luhnCheck(string $number): bool
    {
        $sum = 0;
        $numDigits = strlen($number) - 1;
        $parity = $numDigits % 2;

        for ($i = 0; $i < $numDigits; $i++) {
            $digit = (int) $number[$i];
            if ($i % 2 === $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit === (int) $number[$numDigits];
    }

    public static function fromString(string $number): self
    {
        return new self($number);
    }

    public function toString(): string
    {
        return $this->number;
    }

    public function mask(): string
    {
        $lastFour = substr($this->number, -4);
        return str_repeat('*', strlen($this->number) - 4) . $lastFour;
    }
}
