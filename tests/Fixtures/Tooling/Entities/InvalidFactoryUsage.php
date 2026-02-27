<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;

class InvalidFactoryUsage
{
    public function doSomething(): void
    {
        $factory = Factory::new();
        $instance = new Factory;
    }
}
