<?php

declare(strict_types=1);

namespace App\Modules\Shared\Domain\ValueObject;

class Quantity
{
    public function __construct(private int $value)
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value();
    }
}
