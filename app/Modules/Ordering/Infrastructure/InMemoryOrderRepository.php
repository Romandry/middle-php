<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Infrastructure;

use App\Modules\Ordering\Application\Port\OrderRepository;
use App\Modules\Ordering\Domain\Order;
use App\Modules\Ordering\Domain\ValueObject\OrderId;

final class InMemoryOrderRepository implements OrderRepository
{
    /** @var array<string, Order> */
    private array $storage = [];

    public function save(Order $order): void
    {
        $this->storage[(string) $order->id()] = $order;
    }

    public function get(OrderId $id): ?Order
    {
        $key = (string) $id;

        return $this->storage[$key] ?? null;
    }
}
