<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Infrastructure;

use App\Modules\Ordering\Application\Dto\PlaceOrderResult;
use App\Modules\Ordering\Application\Port\IdempotencyRepository;

final class InMemoryIdempotencyRepository implements IdempotencyRepository
{
    /**
     * @var array<string, PlaceOrderResult>
     */
    private array $storage = [];

    public function has(string $key): bool
    {
        return isset($this->storage[$key]);
    }

    public function get(string $key): PlaceOrderResult
    {
        return $this->storage[$key];
    }

    public function put(string $key, PlaceOrderResult $payload): void
    {
        $this->storage[$key] = $payload;
    }
}
