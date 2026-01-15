<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Application\Port;

use App\Modules\Catalog\Domain\ValueObject\Sku;
use App\Modules\Shared\Domain\ValueObject\Quantity;

interface WarehousePort
{
    /**
     * Reserve stock. Should throw a domain exception if not enough stock.
     */
    public function reserve(Sku $sku, Quantity $quantity): void;

    public function release(Sku $sku, Quantity $quantity): void;
}
