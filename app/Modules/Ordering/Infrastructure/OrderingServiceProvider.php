<?php

declare(strict_types=1);

namespace App\Modules\Ordering\Infrastructure;

use App\Modules\Ordering\Application\Port\PlaceOrderRequestHasher;
use App\Modules\Ordering\Application\Service\Sha256PlaceOrderRequestHasher;
use Carbon\Laravel\ServiceProvider;

final class OrderingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            PlaceOrderRequestHasher::class,
            Sha256PlaceOrderRequestHasher::class
        );
    }
}
