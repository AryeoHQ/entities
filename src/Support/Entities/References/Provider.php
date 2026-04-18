<?php

declare(strict_types=1);

namespace Support\Entities\References;

use Illuminate\Support\Stringable;
use Tooling\GeneratorCommands\References\GenericClass;

final class Provider extends GenericClass
{
    public null|Stringable $subNamespace {
        get => str('Provider');
    }

    public Stringable $stubPath {
        get => str(__DIR__.'/stubs/provider.stub');
    }

    public Entity $entity {
        get => Entity::fromFqcn(
            $this->baseNamespace->append('\\', (string) str((string) $this->baseNamespace->afterLast('\\'))->singular()),
        );
    }
}
