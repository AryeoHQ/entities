<?php

declare(strict_types=1);

namespace Tests\Support\Entities\References\Contracts;

use Support\Entities\References\Contracts\Reference;

interface TestsReference
{
    public Reference $subject { get; }

    public string $expectedName { get; }

    public null|string $expectedSubdirectory { get; }
}
