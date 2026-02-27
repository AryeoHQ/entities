<?php

declare(strict_types=1);

namespace Support\Entities\References\Contracts;

use Illuminate\Support\Stringable;
use Support\Entities\References\TestClass;

interface Reference
{
    public Stringable $name { get; }

    public Stringable $namespace { get; }

    public Stringable $fqcn { get; }

    public Stringable $directory { get; }

    public Stringable $directoryPath { get; }

    public null|Stringable $subdirectory { get; }

    public Stringable $filePath { get; }

    public TestClass $test { get; }
}
