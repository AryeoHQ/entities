<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

class ValidFactoryUsage
{
    public function doSomething(): void
    {
        $factory = ValidModel::factory();
    }
}
