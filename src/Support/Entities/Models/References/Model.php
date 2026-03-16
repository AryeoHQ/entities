<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use Support\Entities\References\Entity;

final class Model extends Entity
{
    public Builder $builder {
        get => new Builder(name: 'Builder', baseNamespace: $this->namespace);
    }

    public Collection $collection {
        get => new Collection(name: $this->plural, baseNamespace: $this->namespace);
    }

    public Factory $factory {
        get => new Factory(name: 'Factory', baseNamespace: $this->namespace);
    }

    public function event(string $event): Event
    {
        return new Event(name: ucfirst($event), baseNamespace: $this->namespace);
    }
}
