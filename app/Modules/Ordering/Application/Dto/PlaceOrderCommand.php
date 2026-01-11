<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Application\Dto;

final class PlaceOrderCommand
{
    /**
     * @param  array<string, int>  $items  sku => quantity
     */
    public function __construct(
        public readonly string $idempotencyKey,
        public readonly array $items
    ) {}
}
