<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Infrastructure;

use App\Modules\Ordering\Application\Port\IdempotencyRepository;

final class InMemoryIdempotencyRepository implements IdempotencyRepository
{
    /**
     * @var array<string, array{orderId:string}>
     */
    private array $storage = [];

    public function has(string $key): bool
    {
        return isset($this->storage[$key]);
    }

    public function get(string $key): array
    {
        return $this->storage[$key];
    }

    public function put(string $key, array $payload): void
    {
        $this->storage[$key] = $payload;
    }
}
