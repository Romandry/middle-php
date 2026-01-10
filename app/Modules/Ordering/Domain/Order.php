<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Domain;

use App\Modules\Shared\Domain\ValueObject\Money;

final class Order
{
    /** @var OrderItem[] */
    private array $items;

    private Money $total;

    /**
     * @param OrderItem[] $items
     */
    private function __construct(array $items, Money $total)
    {
        $this->items = $items;
        $this->total = $total;
    }

    /**
     * @param  OrderItem[]  $items
     */
    public static function place(array $items): self
    {
        if ($items === []) {
            throw new \InvalidArgumentException('Order must have at least one item.');
        }

        $currency = $items[0]->currency();
        $totalAmount = 0;

        foreach ($items as $item) {
            if ($item->currency() !== $currency) {
                throw new \InvalidArgumentException('All order items must have same currency');
            }

            $totalAmount += $item->subTotal()->amount();
        }

        return new self($items, new Money($totalAmount, $currency));
    }

    public function total(): Money
    {
        return $this->total;
    }

    /**
     * @return OrderItem[]
     */
    public function items(): array
    {
        return $this->items;
    }
}
