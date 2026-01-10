<?php

declare(strict_types=1);

namespace App\Modules\Shared\Domain\ValueObject;

final class Money
{
    public function __construct(
        private int $amount,
        private string $currency,
    ) {
        if ($this->amount < 0) {
            throw new \InvalidArgumentException('Money amount cannot be negative');
        }

        $currency = trim($currency);
        if ($currency === '') {
            throw new \InvalidArgumentException('Currency cannot be empty');
        }

        $this->currency = $currency;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function equals(self $other): bool
    {
        return $this->amount() === $other->amount()
            && $this->currency() === $other->currency();
    }
}
