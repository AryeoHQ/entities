<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use Illuminate\Support\Stringable;
use Support\Entities\References\Entity;

final class Model extends Entity
{
    public Stringable $stubPath {
        get => str(__DIR__.'/stubs/model.stub');
    }

    public Builder $builder {
        get => resolve(Builder::class, ['name' => 'Builder', 'baseNamespace' => $this->namespace]);
    }

    public Collection $collection {
        get => resolve(Collection::class, ['name' => $this->plural, 'baseNamespace' => $this->namespace]);
    }

    public Factory $factory {
        get => resolve(Factory::class, ['name' => 'Factory', 'baseNamespace' => $this->namespace]);
    }

    public function event(string $event): Event
    {
        return resolve(Event::class, ['name' => ucfirst($event), 'baseNamespace' => $this->namespace]);
    }
}
