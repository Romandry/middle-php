<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Application\Dto;

final class IdempotencyRecord
{
    public function __construct(
        public readonly string $requestHash,
        public readonly PlaceOrderResult $result
    ) {}
}
