<?php

declare(strict_types=1);

use App\Modules\Ordering\Application\Dto\PlaceOrderResult;
use App\Modules\Ordering\Infrastructure\InMemoryIdempotencyRepository;

test('in-memory idempotency repository stores and returns payload by key', function () {
    $repo = new InMemoryIdempotencyRepository;

    expect($repo->has('KEY-1'))->toBeFalse();

    $resultToStore = new PlaceOrderResult('ORD-123');

    $repo->put('KEY-1', $resultToStore);

    expect($repo->has('KEY-1'))->toBeTrue();

    $result = $repo->get('KEY-1');

    expect($result)->toBeInstanceOf(PlaceOrderResult::class);
    expect($result->orderId())->toBe('ORD-123');
});
