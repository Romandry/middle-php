<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Application\Port;

use App\Modules\Ordering\Domain\Order;

interface OrderRepository
{
    public function save(Order $order): void;
}
