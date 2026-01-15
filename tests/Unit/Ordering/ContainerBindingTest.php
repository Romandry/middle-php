<?php

declare(strict_types=1);

use App\Modules\Ordering\Application\Port\PlaceOrderRequestHasher;
use App\Modules\Ordering\Application\Service\Sha256PlaceOrderRequestHasher;

test('container resolves PlaceOrderRequestHasher to sha256 implementation', function () {
    $hasher = app()->make(PlaceOrderRequestHasher::class);
    expect($hasher)->toBeInstanceOf(Sha256PlaceOrderRequestHasher::class);
});
