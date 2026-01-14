<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Application\Port;

use App\Modules\Ordering\Application\Dto\PlaceOrderCommand;

interface PlaceOrderRequestHasher
{
    public function hash(PlaceOrderCommand $command): string;
}
