<?php

declare(strict_types=1);

use App\Modules\Ordering\Application\Dto\PlaceOrderCommand;
use App\Modules\Ordering\Application\Service\Sha256PlaceOrderRequestHasher;

test('request hasher returns same hash for same items regardles of order', function () {
    $hasher = new Sha256PlaceOrderRequestHasher;

    $hash1 = $hasher->hash(new PlaceOrderCommand('KEY-1', [
        'SKU-1' => 2,
        'SKU-2' => 1,
    ]));
    $hash2 = $hasher->hash(new PlaceOrderCommand('KEY-1', [
        'SKU-2' => 1,
        'SKU-1' => 2,
    ]));

    expect($hash1)->toBeString();
    expect($hash2)->toBe($hash1);
});
