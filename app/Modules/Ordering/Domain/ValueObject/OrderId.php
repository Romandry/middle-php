<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Domain\ValueObject;

use App\Modules\Ordering\Domain\Exception\InvalidOrderId;

final class OrderId
{
    private function __construct(
        private string $value
    ) {}

    public static function generate(): self
    {
        return new self(bin2hex(random_bytes(16)));
    }

    public static function fromString(string $value): self
    {
        if ($value === '') {
            throw new InvalidOrderId('Order id cannot be empty.');
        }

        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
