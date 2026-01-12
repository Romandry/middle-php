<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Infrastructure;

use App\Modules\Catalog\Domain\ValueObject\Sku;
use App\Modules\Ordering\Application\Port\PricingPort;
use App\Modules\Shared\Domain\ValueObject\Money;
use RuntimeException;

final class InMemoryPricingPort implements PricingPort
{
    /** @var array<string, Money> */
    private array $prices;

    /** @param array<string, Money> $prices */
    public function __construct(array $prices = [])
    {
        $this->prices = $prices;
    }

    public function priceForSku(Sku $sku): Money
    {
        $key = (string) $sku;
        if (! isset($this->prices[$key])) {
            throw new RuntimeException('Price not configured for sku: '.$key);
        }

        return $this->prices[$key];
    }
}
