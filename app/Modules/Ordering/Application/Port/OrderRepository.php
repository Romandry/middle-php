<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Application\Port;

use App\Modules\Ordering\Domain\Order;
use App\Modules\Ordering\Domain\ValueObject\OrderId;

interface OrderRepository
{
    public function save(Order $order): void;

    public function get(OrderId $id): ?Order;
}
