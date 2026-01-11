<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Domain\Exception;

final class InvalidOrderId extends \DomainException
{
    public static function create(): self
    {
        return new self('Invalid order id');
    }
}
