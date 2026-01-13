<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Infrastructure;

use App\Modules\Ordering\Application\Dto\IdempotencyRecord;
use App\Modules\Ordering\Application\Port\IdempotencyRepository;

final class InMemoryIdempotencyRepository implements IdempotencyRepository
{
    /**
     * @var array<string, IdempotencyRecord>
     */
    private array $storage = [];

    public function has(string $key): bool
    {
        return isset($this->storage[$key]);
    }

    public function get(string $key): IdempotencyRecord
    {
        return $this->storage[$key];
    }

    public function put(string $key, IdempotencyRecord $payload): void
    {
        $this->storage[$key] = $payload;
    }
}
