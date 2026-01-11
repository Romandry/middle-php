<?php

use App\Modules\Catalog\Domain\ValueObject\Sku;
use App\Modules\Ordering\Application\Dto\PlaceOrderCommand;
use App\Modules\Ordering\Application\PlaceOrder\PlaceOrderHandler;
use App\Modules\Ordering\Application\Port\IdempotencyRepository;
use App\Modules\Ordering\Application\Port\OrderRepository;
use App\Modules\Ordering\Application\Port\PricingPort;
use App\Modules\Ordering\Application\Port\WarehousePort;
use App\Modules\Ordering\Domain\Order;
use App\Modules\Shared\Domain\ValueObject\Money;
use App\Modules\Shared\Domain\ValueObject\Quantity;
use Mockery\MockInterface;

test('returns previous result when idempotency key already exists', function () {
    /** @var IdempotencyRepository & MockInterface $idempotency */
    $idempotency = Mockery::mock(IdempotencyRepository::class);

    /** @var PricingPort & MockInterface $pricing */
    $pricing = Mockery::mock(PricingPort::class);

    /** @var WarehousePort & MockInterface $warehouse */
    $warehouse = Mockery::mock(WarehousePort::class);

    /** @var OrderRepository & MockInterface $orders */
    $orders = Mockery::mock(OrderRepository::class);

    $idempotency->shouldReceive('has')
        ->with('ABC-KEY')
        ->once()
        ->andReturn(true);

    $idempotency->shouldReceive('get')
        ->with('ABC-KEY')
        ->once()
        ->andReturn(['orderId' => 'ORD-123']);

    $pricing->shouldNotReceive('priceForSku');
    $warehouse->shouldNotReceive('reserve');
    $orders->shouldNotReceive('save');

    $handler = new PlaceOrderHandler($pricing, $warehouse, $orders, $idempotency);

    $result = $handler->handle(new PlaceOrderCommand('ABC-KEY', ['SKU-1' => 2]));

    expect($result)->toBe(['orderId' => 'ORD-123']);
});

test('creates order and stores idempotency result for new key', function () {
    /** @var IdempotencyRepository & MockInterface $idempotency */
    $idempotency = Mockery::mock(IdempotencyRepository::class);

    /** @var PricingPort & MockInterface $pricing */
    $pricing = Mockery::mock(PricingPort::class);

    /** @var WarehousePort & MockInterface $warehouse */
    $warehouse = Mockery::mock(WarehousePort::class);

    /** @var OrderRepository & MockInterface $orders */
    $orders = Mockery::mock(OrderRepository::class);

    $idempotency->shouldReceive('has')
        ->with('NEW-KEY')
        ->once()
        ->andReturn(false);

    $pricing->shouldReceive('priceForSku')
        ->once()
        ->with(Mockery::type(Sku::class))
        ->andReturn(new Money(500, 'EUR'));

    $warehouse->shouldReceive('reserve')
        ->once()
        ->with(
            Mockery::type(Sku::class),
            Mockery::type(Quantity::class)
        );

    $orders->shouldReceive('save')
        ->once()
        ->with(Mockery::type(Order::class));

    $idempotency->shouldReceive('put')
        ->once()
        ->with(
            'NEW-KEY',
            Mockery::on(function (array $payload) {
                return isset($payload['orderId']) && is_string($payload['orderId']) && $payload['orderId'] !== '';
            })
        );

    $handler = new PlaceOrderHandler(
        $pricing,
        $warehouse,
        $orders,
        $idempotency
    );

    $result = $handler->handle(
        new PlaceOrderCommand(
            'NEW-KEY',
            ['SKU-1' => 2]
        ));

    expect($result)->toHaveKey('orderId');
    expect($result['orderId'])->toBeString();
});
