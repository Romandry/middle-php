<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Domain;

use App\Modules\Catalog\Domain\ValueObject\Sku;
use App\Modules\Shared\Domain\ValueObject\Money;
use App\Modules\Shared\Domain\ValueObject\Quantity;

final class OrderItem
{
    public function __construct(
        private readonly Sku $sku,
        private readonly Quantity $quantity,
        private readonly Money $unitPrice,
    ) {}

    public function subTotal(): Money
    {
        return new Money(
            $this->unitPrice->amount() * $this->quantity->value(),
            $this->unitPrice->currency()
        );
    }

    public function currency(): string
    {
        return $this->unitPrice->currency();
    }

    public function sku(): Sku
    {
        return $this->sku;
    }

    public function quantity(): Quantity
    {
        return $this->quantity;
    }

    public function unitPrice(): Money
    {
        return $this->unitPrice;
    }
}
