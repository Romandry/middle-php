<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Application\PlaceOrder;

use App\Modules\Catalog\Domain\ValueObject\Sku;
use App\Modules\Ordering\Application\Dto\PlaceOrderCommand;
use App\Modules\Ordering\Application\Dto\PlaceOrderResult;
use App\Modules\Ordering\Application\Port\IdempotencyRepository;
use App\Modules\Ordering\Application\Port\OrderRepository;
use App\Modules\Ordering\Application\Port\PricingPort;
use App\Modules\Ordering\Application\Port\WarehousePort;
use App\Modules\Ordering\Domain\Order;
use App\Modules\Ordering\Domain\OrderItem;
use App\Modules\Shared\Domain\ValueObject\Quantity;

final class PlaceOrderHandler
{
    public function __construct(
        private PricingPort $pricing,
        private WarehousePort $warehouse,
        private OrderRepository $orders,
        private IdempotencyRepository $idempotency
    ) {}

    public function handle(PlaceOrderCommand $command): PlaceOrderResult
    {
        $key = $command->idempotencyKey;

        if ($this->idempotency->has($key)) {
            return $this->idempotency->get($key);
        }

        $items = [];

        foreach ($command->items as $skuString => $qtyInt) {
            $sku = new Sku((string) $skuString);

            $qty = new Quantity($qtyInt);

            $price = $this->pricing->priceForSku($sku);

            $this->warehouse->reserve($sku, $qty);

            $items[] = new OrderItem($sku, $qty, $price);
        }

        $order = Order::place($items);

        $this->orders->save($order);

        $orderId = (string) $order->id();

        $result = new PlaceOrderResult($orderId);

        $this->idempotency->put($key, $result);

        return $result;
    }
}
