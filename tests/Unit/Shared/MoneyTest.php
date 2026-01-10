<?php

use App\Modules\Shared\Domain\ValueObject\Money;

test('money amount cannot be negative ', function () {
    new Money(-1, 'EUR');
})->throws(InvalidArgumentException::class);

test('money currency cannot be empty', function () {
    new Money(1099, '');
})->throws(InvalidArgumentException::class);

test('money returns amount and currency', function () {
    $money = new Money(1099, 'EUR');

    expect($money->amount())->toBe(1099);
    expect($money->currency())->toBe('EUR');
});

test('two money objects with same amount and currency are equal', function () {
    $a = new Money(1099, 'EUR');
    $b = new Money(1099, 'EUR');

    expect($a->equals($b))->toBeTrue();
});
