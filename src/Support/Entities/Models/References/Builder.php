<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use Illuminate\Support\Stringable;
use Tooling\GeneratorCommands\References\GenericClass;

final class Builder extends GenericClass
{
    public null|Stringable $subNamespace {
        get => str('Builder');
    }

    public Stringable $stubPath {
        get => str(__DIR__.'/stubs/builder.stub');
    }

    public Model $model {
        get => Model::fromFqcn(
            $this->baseNamespace->append('\\', (string) str((string) $this->baseNamespace->afterLast('\\'))->singular()),
        );
    }
}
