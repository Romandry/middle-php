<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Application\Port;

interface IdempotencyRepository
{
    public function has(string $key): bool;

    /**
     * Returns previously stored response payload for the key
     *
     * @return array{orderId: string}
     */
    public function get(string $key): array;

    /**
     * Stored response payload for the key
     *
     * @param  array{orderId: string}  $payload
     */
    public function put(string $key, array $payload): void;
}
