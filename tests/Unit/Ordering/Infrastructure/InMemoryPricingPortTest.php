<?php

declare(strict_types=1);

use App\Modules\Catalog\Domain\ValueObject\Sku;
use App\Modules\Ordering\Application\Port\PricingPort;
use App\Modules\Ordering\Infrastructure\InMemoryPricingPort;
use App\Modules\Shared\Domain\ValueObject\Money;

test('in-memory pricing port returns configured price for sku', function () {
    // Arrange
    $pricing = new InMemoryPricingPort(
        ['SKU-1' => new Money(500, 'EUR')]
    );

    expect($pricing)->toBeInstanceOf(PricingPort::class);

    // Act
    $price = $pricing->priceForSku(new Sku('SKU-1'));

    // Assert
    expect($price)->toBeInstanceOf(Money::class);
    expect($price->amount())->toBe(500);
    expect($price->currency())->toBe('EUR');
});
