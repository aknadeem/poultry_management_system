<?php

namespace App\Support;

use InvalidArgumentException;

final class FinancialAmount
{
    private function __construct(private readonly int $cents)
    {
    }

    public static function fromDecimalString(string|int|float $value): self
    {
        if (is_int($value)) {
            return new self($value * 100);
        }

        $normalized = is_float($value)
            ? number_format($value, 2, '.', '')
            : trim((string) $value);

        if ($normalized === '' || ! preg_match('/^-?\d+(\.\d{1,2})?$/', $normalized)) {
            throw new InvalidArgumentException("Invalid monetary amount [{$normalized}].");
        }

        $negative = str_starts_with($normalized, '-');
        $normalized = ltrim($normalized, '-');
        [$whole, $fraction] = array_pad(explode('.', $normalized, 2), 2, '0');
        $fraction = str_pad($fraction, 2, '0');

        $cents = ((int) $whole * 100) + (int) $fraction;
        if ($negative) {
            $cents *= -1;
        }

        return new self($cents);
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function add(self $other): self
    {
        return new self($this->cents + $other->cents);
    }

    public function subtract(self $other): self
    {
        return new self($this->cents - $other->cents);
    }

    public function equals(self $other): bool
    {
        return $this->cents === $other->cents;
    }

    public function greaterThan(self $other): bool
    {
        return $this->cents > $other->cents;
    }

    public function greaterThanOrEqual(self $other): bool
    {
        return $this->cents >= $other->cents;
    }

    public function lessThan(self $other): bool
    {
        return $this->cents < $other->cents;
    }

    public function isZero(): bool
    {
        return $this->cents === 0;
    }

    public function isNegative(): bool
    {
        return $this->cents < 0;
    }

    public function isPositive(): bool
    {
        return $this->cents > 0;
    }

    public function toDecimalString(): string
    {
        $negative = $this->cents < 0;
        $absolute = abs($this->cents);
        $whole = intdiv($absolute, 100);
        $fraction = $absolute % 100;

        return sprintf('%s%d.%02d', $negative ? '-' : '', $whole, $fraction);
    }

    public function toFloat(): float
    {
        return (float) $this->toDecimalString();
    }
}
