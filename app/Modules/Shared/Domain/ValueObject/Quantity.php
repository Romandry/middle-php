<?php

declare(strict_types=1);

namespace App\Modules\Shared\Domain\ValueObject;

use App\Modules\Shared\Domain\Exception\QuantityMustBePositive;

final class Quantity
{
    public function __construct(private int $value)
    {
        if ($value <= 0) {
            throw QuantityMustBePositive::create();
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
