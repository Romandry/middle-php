<?php

declare(strict_types=1);

use App\Modules\Ordering\Infrastructure\InMemoryIdempotencyRepository;

test('in-memory idempotency repository stores and returns payload by key', function () {
    $repo = new InMemoryIdempotencyRepository;

    expect($repo->has('KEY-1'))->toBeFalse();

    $repo->put('KEY-1', ['orderId' => 'ORD-123']);

    expect($repo->has('KEY-1'))->toBeTrue();

    expect($repo->get('KEY-1'))->toBe(['orderId' => 'ORD-123']);
});
