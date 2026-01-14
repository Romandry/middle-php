<?php

declare(strict_types=1);

use App\Modules\Catalog\Domain\ValueObject\Sku;
use App\Modules\Ordering\Application\Dto\PlaceOrderCommand;
use App\Modules\Ordering\Application\Exception\IdempotencyKeyConflict;
use App\Modules\Ordering\Application\PlaceOrder\PlaceOrderHandler;
use App\Modules\Ordering\Application\Service\Sha256PlaceOrderRequestHasher;
use App\Modules\Ordering\Infrastructure\InMemoryIdempotencyRepository;
use App\Modules\Ordering\Infrastructure\InMemoryOrderRepository;
use App\Modules\Ordering\Infrastructure\InMemoryPricingPort;
use App\Modules\Ordering\Infrastructure\InMemoryWarehousePort;
use App\Modules\Shared\Domain\ValueObject\Money;

test('place order end-to-end: reserves stock, saves order and is idempotent', function () {
    // Arrange
    $idempotency = new InMemoryIdempotencyRepository;
    $orders = new InMemoryOrderRepository;
    $hasher = new Sha256PlaceOrderRequestHasher;

    $pricing = new InMemoryPricingPort(
        ['SKU-1' => new Money(500, 'EUR')]
    );
    $warehouse = new InMemoryWarehousePort(
        ['SKU-1' => 10]
    );

    $handler = new PlaceOrderHandler($pricing, $warehouse, $orders, $idempotency, $hasher);

    // Act1
    $result1 = $handler->handle(new PlaceOrderCommand('KEY-1', ['SKU-1' => 2]));

    // Assert
    expect($result1->orderId())->toBeString();
    expect($result1->orderId())->not->toBe('');
    expect($warehouse->available(new Sku('SKU-1'))->value())->toBe(8);

    // Act2 the same (idempotency)
    $result2 = $handler->handle(new PlaceOrderCommand('KEY-1', ['SKU-1' => 2]));

    // Assert
    expect($result2->orderId())->toBe($result1->orderId());
    // second reserve does not run.
    expect($warehouse->available(new Sku('SKU-1'))->value())->toBe(8);
});

test('place order end-to-end: throws conflict when key reused with different payload', function () {
    // Arrange
    $idempotency = new InMemoryIdempotencyRepository;
    $orders = new InMemoryOrderRepository;
    $hasher = new Sha256PlaceOrderRequestHasher;

    $pricing = new InMemoryPricingPort(
        ['SKU-1' => new Money(500, 'EUR')]
    );
    $warehouse = new InMemoryWarehousePort(
        ['SKU-1' => 10]
    );

    $handler = new PlaceOrderHandler($pricing, $warehouse, $orders, $idempotency, $hasher);
    $handler->handle(new PlaceOrderCommand('KEY-1', ['SKU-1' => 2]));
    $handler->handle(new PlaceOrderCommand('KEY-1', ['SKU-2' => 2]));

})->throws(IdempotencyKeyConflict::class);
