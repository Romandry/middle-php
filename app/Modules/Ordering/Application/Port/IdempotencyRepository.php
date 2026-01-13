<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Application\Port;

use App\Modules\Ordering\Application\Dto\PlaceOrderResult;

interface IdempotencyRepository
{
    public function has(string $key): bool;

    /**
     * Returns previously stored response payload for the key
     */
    public function get(string $key): PlaceOrderResult;

    /**
     * Stored response payload for the key
     */
    public function put(string $key, PlaceOrderResult $payload): void;
}
