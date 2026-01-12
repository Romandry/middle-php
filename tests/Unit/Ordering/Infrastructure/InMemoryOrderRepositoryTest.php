<?php

declare(strict_types=1);

use App\Modules\Catalog\Domain\ValueObject\Sku;
use App\Modules\Ordering\Application\Port\OrderRepository;
use App\Modules\Ordering\Domain\Order;
use App\Modules\Ordering\Domain\OrderItem;
use App\Modules\Ordering\Infrastructure\InMemoryOrderRepository;
use App\Modules\Shared\Domain\ValueObject\Money;
use App\Modules\Shared\Domain\ValueObject\Quantity;

test('in-memory order repository can save an order', function () {
    // Arrange
    $item = new OrderItem(
        new Sku('SKU-1'),
        new Quantity(2),
        new Money(500, 'EUR')
    );

    $order = Order::place([$item]);

    $repo = new InMemoryOrderRepository;

    expect($repo)->toBeInstanceOf(OrderRepository::class);

    // Act
    $repo->save($order);

    // Assert
    expect(true)->toBe(true);
});

test('in-memory order repository returns saved order by id', function () {
    // Arrange
    $item = new OrderItem(
        new Sku('SKU-1'),
        new Quantity(2),
        new Money(500, 'EUR')
    );
    $order = Order::place([$item]);

    $repo = new InMemoryOrderRepository;

    // Act
    $repo->save($order);
    $found = $repo->get($order->id());

    // Assert
    expect($found)->not()->toBeNull();
    expect($found)->toBeInstanceOf(Order::class);
    expect((string) $found->id())->toBe((string) $order->id());
});
