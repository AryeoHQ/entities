<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use Illuminate\Support\Stringable;
use Tooling\GeneratorCommands\References\GenericClass;

final class Collection extends GenericClass
{
    public null|Stringable $subNamespace {
        get => str('Collection');
    }

    public Stringable $stubPath {
        get => str(__DIR__.'/stubs/collection.stub');
    }

    public Model $model {
        get => Model::fromFqcn(
            $this->baseNamespace->append('\\', (string) str((string) $this->baseNamespace->afterLast('\\'))->singular()),
        );
    }
}
