<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use Illuminate\Support\Stringable;
use Tooling\GeneratorCommands\References\GenericClass;

final class Factory extends GenericClass
{
    public null|Stringable $subNamespace {
        get => str('Factory');
    }

    public Stringable $stubPath {
        get => str(__DIR__.'/stubs/factory.stub');
    }

    public Model $model {
        get => Model::fromFqcn(
            $this->baseNamespace->append('\\', (string) str((string) $this->baseNamespace->afterLast('\\'))->singular()),
        );
    }
}
