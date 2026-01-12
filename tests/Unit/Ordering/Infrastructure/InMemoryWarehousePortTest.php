<?php

declare(strict_types=1);

use App\Modules\Catalog\Domain\ValueObject\Sku;
use App\Modules\Ordering\Application\Port\WarehousePort;
use App\Modules\Ordering\Infrastructure\InMemoryWarehousePort;
use App\Modules\Shared\Domain\ValueObject\Quantity;

test('warehouse reserves stock when enough quantity is available', function () {
    // Arrange
    $warehouse = new InMemoryWarehousePort(
        ['SKU-1' => 10]
    );

    expect($warehouse)->toBeInstanceOf(WarehousePort::class);

    // Act
    $warehouse->reserve(
        new Sku('SKU-1'),
        new Quantity(3)
    );

    // Assert
    expect($warehouse->available(new Sku('SKU-1'))->value())->toBe(7);
});
