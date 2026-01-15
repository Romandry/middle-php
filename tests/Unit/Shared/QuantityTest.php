<?php

use App\Modules\Shared\Domain\Exception\QuantityMustBePositive;
use App\Modules\Shared\Domain\ValueObject\Quantity;

test('quantity must be positive', function () {
    new Quantity(0);
})->throws(QuantityMustBePositive::class);

test('quantity cannot be negative', function () {
    new Quantity(-1);
})->throws(QuantityMustBePositive::class);

test('quantity keeps integer value', function () {
    $q = new Quantity(3);
    expect($q->value())->toBe(3);
});
