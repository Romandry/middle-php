<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Application\Exception;

use RuntimeException;

final class InsufficientStock extends RuntimeException
{
    public static function forSku(string $sku, int $available, int $requested): self
    {
        return new self(
            sprintf(
                'Insufficient Stock for SKU "%s". Available "%d", requested "%d"',
                $sku,
                $available,
                $requested
            )
        );
    }
}
