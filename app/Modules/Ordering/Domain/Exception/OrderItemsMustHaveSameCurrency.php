<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Domain\Exception;

final class OrderItemsMustHaveSameCurrency extends \DomainException
{
    public static function create(string $expected, string $actual): self
    {
        return new self(
            sprintf(
                'All order items must have same currency. Expected "%s", got "%s"',
                $expected,
                $actual
            )
        );
    }
}
