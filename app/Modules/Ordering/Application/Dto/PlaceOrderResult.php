<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Application\Dto;

final class PlaceOrderResult
{
    private string $orderId;

    public function __construct(string $orderId)
    {
        if ($orderId === '') {
            throw new \InvalidArgumentException('Order ID cannot be empty.');
        }
        $this->orderId = $orderId;
    }

    public function orderId(): string
    {
        return $this->orderId;
    }

    /**
     * @return array{orderId: string}
     */
    public function toArray(): array
    {
        return ['orderId' => $this->orderId];
    }
}
