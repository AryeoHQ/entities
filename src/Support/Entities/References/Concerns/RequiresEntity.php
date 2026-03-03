<?php

declare(strict_types=1);

namespace Support\Entities\References\Concerns;

use Illuminate\Support\Stringable;
use Support\Entities\References\Contracts\Entity;
use Tooling\Composer\Composer;
use Tooling\GeneratorCommands\References\TestClass;

trait RequiresEntity
{
    public Entity $entity;

    public Stringable $namespace {
        get => $this->subdirectory // @phpstan-ignore ternary.alwaysTrue
            ? $this->entity->namespace->append('\\', $this->subdirectory->toString())
            : $this->entity->namespace;
    }

    public Stringable $fqcn {
        get => $this->namespace->append('\\', $this->name->toString());
    }

    public Stringable $directory {
        get => $this->subdirectory // @phpstan-ignore ternary.alwaysTrue
            ? $this->entity->directory->append('/', $this->subdirectory->toString())
            : $this->entity->directory;
    }

    public Stringable $directoryPath {
        get => resolve(Composer::class)->baseDirectory->append('/', $this->directory->toString());
    }

    public Stringable $filePath {
        get => $this->directoryPath->append('/', $this->name->toString(), '.php');
    }

    public TestClass $test {
        get => new TestClass($this);
    }
}
