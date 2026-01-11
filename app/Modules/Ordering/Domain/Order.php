<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Domain;

use App\Modules\Ordering\Domain\Exception\OrderCannotBeEmpty;
use App\Modules\Ordering\Domain\ValueObject\OrderId;
use App\Modules\Shared\Domain\ValueObject\Money;

final class Order
{
    private OrderId $id;

    /** @var OrderItem[] */
    private array $items;

    private Money $total;

    /**
     * @param  OrderItem[]  $items
     */
    private function __construct(OrderId $id, array $items, Money $total)
    {
        $this->id = $id;
        $this->items = $items;
        $this->total = $total;
    }

    /**
     * @param  OrderItem[]  $items
     */
    public static function place(array $items): self
    {
        if ($items === []) {
            throw OrderCannotBeEmpty::create();
        }

        $id = OrderId::generate();

        $currency = $items[0]->currency();
        $totalAmount = 0;

        foreach ($items as $item) {
            if ($item->currency() !== $currency) {
                throw new \InvalidArgumentException('All order items must have same currency');
            }

            $totalAmount += $item->subTotal()->amount();
        }

        return new self($id, $items, new Money($totalAmount, $currency));
    }

    public function id(): OrderId
    {
        return $this->id;
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
