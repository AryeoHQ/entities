<?php

declare(strict_types=1);

namespace Support\Entities\References;

use Illuminate\Support\Stringable;
use Support\Entities\References\Concerns\AsEntity;
use Tooling\GeneratorCommands\References\GenericClass;

class Entity extends GenericClass
{
    use AsEntity;

    public null|Stringable $subNamespace {
        get => str("Entities\\{$this->plural}");
    }

    public Stringable $stubPath {
        get => str(__DIR__.'/stubs/entity.stub');
    }
}
