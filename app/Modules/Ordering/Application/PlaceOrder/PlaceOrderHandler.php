<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Application\PlaceOrder;

use App\Modules\Catalog\Domain\ValueObject\Sku;
use App\Modules\Ordering\Application\Dto\PlaceOrderCommand;
use App\Modules\Ordering\Application\Port\IdempotencyRepository;
use App\Modules\Ordering\Application\Port\OrderRepository;
use App\Modules\Ordering\Application\Port\PricingPort;
use App\Modules\Ordering\Application\Port\WarehousePort;
use App\Modules\Ordering\Domain\Order;
use App\Modules\Ordering\Domain\OrderItem;
use App\Modules\Shared\Domain\ValueObject\Quantity;

/**
 * @phpstan-type PlaceOrderResult array{orderId: string}
 */
final class PlaceOrderHandler
{
    public function __construct(
        private PricingPort $pricing,
        private WarehousePort $warehouse,
        private OrderRepository $orders,
        private IdempotencyRepository $idempotency
    ) {}

    /**
     * @return array{orderId: string}
     */
    public function handle(PlaceOrderCommand $command): array
    {
        $key = $command->idempotencyKey;

        if ($this->idempotency->has($key)) {
            /** @var array{orderId: string} $previous */
            $previous = $this->idempotency->get($key);

            return $previous;
        }

        $items = [];

        foreach ($command->items as $skuString => $qtyInt) {
            $sku = new Sku((string) $skuString);

            $qty = new Quantity((int) $qtyInt);

            $price = $this->pricing->priceForSku($sku);

            $this->warehouse->reserve($sku, $qty);

            $items[] = new OrderItem($sku, $qty, $price);
        }

        $order = Order::place($items);

        $this->orders->save($order);

        $orderId = bin2hex(random_bytes(16));

        $result = ['orderId' => $orderId];

        $this->idempotency->put($key, $result);

        return $result;
    }
}
