<?php

declare(strict_types=1);

namespace Support\Entities\References\Concerns;

use Illuminate\Support\Stringable;
use Support\Entities\References\Policy;
use Support\Entities\References\Provider;

trait AsEntity
{
    public Stringable $singular {
        get => $this->name;
    }

    public Stringable $plural {
        get => $this->name->plural();
    }

    public Stringable $variableName {
        get => $this->name->lower();
    }

    public Policy $policy {
        get => resolve(Policy::class, ['name' => 'Policy', 'baseNamespace' => $this->namespace]);
    }

    public Provider $provider {
        get => resolve(Provider::class, ['name' => 'Provider', 'baseNamespace' => $this->namespace]);
    }
}
