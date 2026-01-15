<?php

use App\Modules\Catalog\Domain\ValueObject\Sku;
use App\Modules\Ordering\Application\Dto\IdempotencyRecord;
use App\Modules\Ordering\Application\Dto\PlaceOrderCommand;
use App\Modules\Ordering\Application\Dto\PlaceOrderResult;
use App\Modules\Ordering\Application\Exception\IdempotencyKeyConflict;
use App\Modules\Ordering\Application\Exception\InsufficientStock;
use App\Modules\Ordering\Application\PlaceOrder\PlaceOrderHandler;
use App\Modules\Ordering\Application\Port\IdempotencyRepository;
use App\Modules\Ordering\Application\Port\OrderRepository;
use App\Modules\Ordering\Application\Port\PlaceOrderRequestHasher;
use App\Modules\Ordering\Application\Port\PricingPort;
use App\Modules\Ordering\Application\Port\WarehousePort;
use App\Modules\Ordering\Application\Service\Sha256PlaceOrderRequestHasher;
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

    $hasher = new Sha256PlaceOrderRequestHasher;

    $idempotency->shouldReceive('has')
        ->with('ABC-KEY')
        ->once()
        ->andReturn(true);

    $idempotency->shouldReceive('get')
        ->with('ABC-KEY')
        ->once()
        ->andReturn(new IdempotencyRecord(
            hash('sha256', json_encode(['SKU-1' => 2], JSON_THROW_ON_ERROR)),
            new PlaceOrderResult('ORD-123')
        ));

    $pricing->shouldNotReceive('priceForSku');
    $warehouse->shouldNotReceive('reserve');
    $orders->shouldNotReceive('save');

    $handler = new PlaceOrderHandler($pricing, $warehouse, $orders, $idempotency, $hasher);

    $result = $handler->handle(new PlaceOrderCommand('ABC-KEY', ['SKU-1' => 2]));

    expect($result)->toBeInstanceOf(PlaceOrderResult::class);
    expect($result->orderId())->toBe('ORD-123');
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

    $hasher = new Sha256PlaceOrderRequestHasher;

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
            Mockery::on(function ($record) {
                return $record instanceof IdempotencyRecord
                    && $record->result->orderId() !== ''
                    && $record->requestHash !== '';
            })
        );

    $handler = new PlaceOrderHandler(
        $pricing,
        $warehouse,
        $orders,
        $idempotency,
        $hasher
    );

    $result = $handler->handle(
        new PlaceOrderCommand(
            'NEW-KEY',
            ['SKU-1' => 2]
        ));

    //    expect($result)->toHaveKey('orderId');
    //    expect($result['orderId'])->toBeString();
    expect($result)->toBeInstanceOf(PlaceOrderResult::class);
    expect($result->orderId())->toBeString();
    expect($result->orderId())->not->toBe('');
});

test('throws conflict when idempotency key is reused with different request payload', function () {
    /** @var IdempotencyRepository & MockInterface $idempotency */
    $idempotency = Mockery::mock(IdempotencyRepository::class);

    /** @var PricingPort & MockInterface $pricing */
    $pricing = Mockery::mock(PricingPort::class);

    /** @var WarehousePort & MockInterface $warehouse */
    $warehouse = Mockery::mock(WarehousePort::class);

    /** @var OrderRepository & MockInterface $orders */
    $orders = Mockery::mock(OrderRepository::class);

    $hasher = new Sha256PlaceOrderRequestHasher;

    $idempotency->shouldReceive('has')
        ->with('ABC-KEY')
        ->once()
        ->andReturn(true);

    $storedHash = hash('sha256', json_encode(['SKU-1' => 2], JSON_THROW_ON_ERROR));

    $idempotency->shouldReceive('get')
        ->with('ABC-KEY')
        ->once()
        ->andReturn(new IdempotencyRecord(
            $storedHash,
            new PlaceOrderResult('ORD-123')
        ));

    $pricing->shouldNotReceive('priceForSku');
    $warehouse->shouldNotReceive('reserve');
    $orders->shouldNotReceive('save');

    $handler = new PlaceOrderHandler($pricing, $warehouse, $orders, $idempotency, $hasher);

    $handler->handle(new PlaceOrderCommand('ABC-KEY', ['SKU-1' => 222]));
})->throws(IdempotencyKeyConflict::class);

test('releases reserved stock when saving order fails', function () {
    /** @var IdempotencyRepository & MockInterface $idempotency */
    $idempotency = Mockery::mock(IdempotencyRepository::class);

    /** @var PricingPort & MockInterface $pricing */
    $pricing = Mockery::mock(PricingPort::class);

    /** @var WarehousePort & MockInterface $warehouse */
    $warehouse = Mockery::mock(WarehousePort::class);

    /** @var OrderRepository & MockInterface $orders */
    $orders = Mockery::mock(OrderRepository::class);

    $hasher = Mockery::mock(PlaceOrderRequestHasher::class);

    $hasher->shouldReceive('hash')
        ->once()
        ->andReturn('hash-1');

    $idempotency->shouldReceive('has')
        ->once()
        ->andReturn(false);

    $pricing->shouldReceive('priceForSku')
        ->once()
        ->andReturn(new Money(500, 'EUR'));

    $warehouse->shouldReceive('reserve')
        ->once()
        ->with(Mockery::type(Sku::class), Mockery::type(Quantity::class));

    $warehouse->shouldReceive('release')
        ->once()
        ->with(Mockery::type(Sku::class), Mockery::type(Quantity::class));

    $orders->shouldReceive('save')
        ->once()
        ->andThrow(new RuntimeException('DB DOWN')); // Save down

    $idempotency->shouldNotReceive('put'); // idempotency does not put - order do not created

    $handler = new PlaceOrderHandler($pricing, $warehouse, $orders, $idempotency, $hasher);

    $handler->handle(new PlaceOrderCommand('NEW-KEY', ['SKU-1' => 2]));
})->throws(RuntimeException::class);

test('releases only successfully reserved items when reservation fails mid-way', function () {
    $idempotency = Mockery::mock(IdempotencyRepository::class);
    $pricing = Mockery::mock(PricingPort::class);
    $warehouse = Mockery::mock(WarehousePort::class);
    $orders = Mockery::mock(OrderRepository::class);
    $hasher = Mockery::mock(PlaceOrderRequestHasher::class);

    $hasher->shouldReceive('hash')
        ->once()
        ->andReturn('hash-1');

    $idempotency->shouldReceive('has')
        ->once()
        ->andReturn(false);

    $pricing->shouldReceive('priceForSku')
        ->twice()
        ->andReturn(new Money(500, 'EUR'));

    $warehouse->shouldReceive('reserve')
        ->once()
        ->with(
            Mockery::on(fn ($sku) => (string) $sku === 'SKU-1'),
            Mockery::type(Quantity::class)
        );
    $warehouse->shouldReceive('reserve')
        ->once()
        ->with(
            Mockery::on(fn ($sku) => (string) $sku === 'SKU-2'),
            Mockery::type(Quantity::class)
        )
        ->andThrow(new InsufficientStock('SKU-2'));

    // Release should be ONLY for SKU-1
    $warehouse->shouldReceive('release')
        ->once()
        ->with(
            Mockery::on(fn ($sku) => (string) $sku === 'SKU-1'),
            Mockery::type(Quantity::class)
        );

    $orders->shouldNotReceive('save');
    $idempotency->shouldNotReceive('put');

    $handler = new PlaceOrderHandler($pricing, $warehouse, $orders, $idempotency, $hasher);
    $handler->handle(new PlaceOrderCommand(
        'ABC-KEY', [
            'SKU-1' => 2,
            'SKU-2' => 1,
        ]));

})->throws(InsufficientStock::class);
