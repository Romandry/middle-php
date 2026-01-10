<?php

use App\Modules\Catalog\Domain\ValueObject\Sku;

test('sku cannot be empty', function () {
    new Sku('');
})->throws(\InvalidArgumentException::class);

test('sku is trimmed', function () {
    $sku = new Sku('   ABC-123   ');
    expect((string)$sku)->toBe('ABC-123');
});

test('two skus with te same value are equal', function () {
    $a = new Sku('ABC-123');
    $b = new Sku('ABC-123');
    expect($a->equals($b))->toBeTrue();
});
