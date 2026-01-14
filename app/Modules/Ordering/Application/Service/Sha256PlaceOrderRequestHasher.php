<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Application\Service;

use App\Modules\Ordering\Application\Dto\PlaceOrderCommand;

final class Sha256PlaceOrderRequestHasher
{
    public function hash(PlaceOrderCommand $command): string
    {
        $items = $command->items;
        ksort($items);

        $normalized = [];
        foreach ($items as $sku => $qty) {
            $normalized[(string) $sku] = (int) $qty;
        }
        $json = json_encode($normalized, JSON_THROW_ON_ERROR);

        return hash('sha256', $json);
    }
}
