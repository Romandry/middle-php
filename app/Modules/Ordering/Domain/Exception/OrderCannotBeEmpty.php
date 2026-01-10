<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Domain\Exception;

final class OrderCannotBeEmpty extends \DomainException
{
    public static function create(): self
    {
        return new self('Order must have at least one item');
    }
}
