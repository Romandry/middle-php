<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Infrastructure;

use App\Modules\Catalog\Domain\ValueObject\Sku;
use App\Modules\Ordering\Application\Exception\InsufficientStock;
use App\Modules\Ordering\Application\Port\WarehousePort;
use App\Modules\Shared\Domain\ValueObject\Quantity;

final class InMemoryWarehousePort implements WarehousePort
{
    /** @var array<string, int> */
    private array $stock;

    /** @param array<string, int> $stock */
    public function __construct(array $stock = [])
    {
        $this->stock = $stock;
    }

    public function reserve(Sku $sku, Quantity $quantity): void
    {
        $key = (string) $sku;

        $available = $this->stock[$key] ?? 0;
        $requested = $quantity->value();

        if ($requested > $available) {
            throw InsufficientStock::forSku($key, $available, $requested);
        }

        $this->stock[$key] = $available - $requested;
    }

    public function available(Sku $sku): Quantity
    {
        $key = (string) $sku;

        $available = $this->stock[$key] ?? 0;

        return new Quantity($available);
    }
}
