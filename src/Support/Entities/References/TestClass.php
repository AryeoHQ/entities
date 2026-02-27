<?php

declare(strict_types=1);

namespace Support\Entities\References;

use Illuminate\Support\Stringable;
use Support\Entities\References\Contracts\Reference;

final class TestClass
{
    public function __construct(
        private readonly Reference $parent,
    ) {}

    public Stringable $name {
        get => $this->parent->name->append('Test');
    }

    public Stringable $namespace {
        get => $this->parent->namespace;
    }

    public Stringable $fqcn {
        get => $this->namespace->append('\\', $this->name->toString());
    }

    public Stringable $directoryPath {
        get => $this->parent->directoryPath;
    }

    public Stringable $filePath {
        get => $this->directoryPath->append('/', $this->name->toString(), '.php');
    }
}
