<?php

use App\Modules\Catalog\Domain\ValueObject\Sku;
use App\Modules\Ordering\Domain\Order;
use App\Modules\Ordering\Domain\OrderItem;
use App\Modules\Shared\Domain\ValueObject\Money;
use App\Modules\Shared\Domain\ValueObject\Quantity;

test('order cannot be created without items', function () {
    Order::place([]);
})->throws(InvalidArgumentException::class);

test('order total is sum of item price * quantity', function () {
    $items = [
        new OrderItem(new Sku('ABC-123'), new Quantity(2), new Money(500, 'EUR')),
        new OrderItem(new Sku('XYZ-987'), new Quantity(1), new Money(250, 'EUR')),
    ];

    $order = Order::place($items);
    expect($order->total()->amount())->toBe(1250);
    expect($order->total()->currency())->toBe('EUR');
});
