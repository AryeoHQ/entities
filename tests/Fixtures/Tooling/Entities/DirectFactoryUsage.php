<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

class DirectFactoryUsage
{
    public function usingNew(): void
    {
        $factory = ValidFactory::new();
    }

    public function usingConstructor(): void
    {
        $factory = new ValidFactory;
    }

    public function usingModelFactory(): void
    {
        $factory = ValidModel::factory();
    }
}
