<?php

declare(strict_types=1);

namespace Support\Entities\References;

use Tooling\GeneratorCommands\References\GenericClass;

final class Provider extends GenericClass
{
    public Entity $entity {
        get => Entity::fromFqcn(
            $this->baseNamespace->append('\\', (string) str((string) $this->baseNamespace->afterLast('\\'))->singular()),
        );
    }
}
