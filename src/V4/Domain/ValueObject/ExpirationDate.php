<?php

namespace PaySimple\V4\Domain\ValueObject;

use DateTimeImmutable;
use PaySimple\V4\Domain\Exception\EntityException;

final class ExpirationDate
{
    private int $month;
    private int $year;

    private function __construct(int $month, int $year)
    {
        $this->validate($month, $year);
        $this->month = $month;
        $this->year = $year;
    }

    private function validate(int $month, int $year): void
    {
        if ($month < 1 || $month > 12) {
            throw EntityException::invalidProperty('ExpirationDate month', (string) $month);
        }

        $currentYear = (int) date('y');
        if ($year < $currentYear) {
            throw EntityException::invalidProperty('ExpirationDate year', (string) $year);
        }
    }

    public static function fromString(string $date): self
    {
        if (preg_match('/^(\d{2})\/?(\d{2,4})$/', $date, $matches)) {
            $month = (int) $matches[1];
            $year = (int) $matches[2];
            
            if ($year < 100) {
                $year = $year;
            } else {
                $year = $year % 100;
            }
            
            return new self($month, $year);
        }

        throw EntityException::invalidProperty('ExpirationDate', $date);
    }

    public static function fromMonthYear(int $month, int $year): self
    {
        return new self($month, $year);
    }

    public function toString(): string
    {
        return sprintf('%02d%02d', $this->month, $this->year);
    }

    public function format(string $format = 'm/y'): string
    {
        return sprintf('%02d/%02d', $this->month, $this->year);
    }

    public function isExpired(): bool
    {
        $now = new DateTimeImmutable();
        $currentMonth = (int) $now->format('m');
        $currentYear = (int) $now->format('y');

        if ($this->year > $currentYear) {
            return false;
        }

        if ($this->year === $currentYear && $this->month >= $currentMonth) {
            return false;
        }

        return true;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getYear(): int
    {
        return $this->year;
    }
}
