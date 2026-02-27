<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use Illuminate\Support\Stringable;
use Support\Entities\References\Concerns\AsEntity;
use Support\Entities\References\Contracts\Entity;

final class Model implements Entity
{
    use AsEntity;

    public Stringable $name;

    public Stringable $baseNamespace;

    public function __construct(Stringable|string $name, Stringable|string $baseNamespace)
    {
        $this->name = str($name);
        $this->baseNamespace = str($baseNamespace);
    }

    public Builder $builder {
        get => new Builder($this);
    }

    public Collection $collection {
        get => new Collection($this);
    }

    public Factory $factory {
        get => new Factory($this);
    }

    public function event(string $event): Event
    {
        return new Event($this, $event);
    }
}
