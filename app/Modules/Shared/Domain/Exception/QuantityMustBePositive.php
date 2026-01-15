<?php

declare(strict_types=1);

namespace App\Modules\Shared\Domain\Exception;

final class QuantityMustBePositive extends \DomainException
{
    public static function create(): self
    {
        return new self('Quantity must be positive');
    }
}
