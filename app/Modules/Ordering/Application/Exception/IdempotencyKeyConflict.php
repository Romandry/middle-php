<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Application\Exception;

use RuntimeException;

final class IdempotencyKeyConflict extends RuntimeException
{
    public static function forKey(string $key): self
    {
        return new self('Idempotency key conflicts with key: "'.$key.'".');
    }
}
