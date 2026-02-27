<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use Illuminate\Support\Stringable;
use Support\Entities\References\Concerns\RequiresEntity;
use Support\Entities\References\Contracts\Entity;
use Support\Entities\References\Contracts\Reference;

final class Factory implements Reference
{
    use RequiresEntity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public Stringable $name {
        get => str('Factory');
    }

    public Stringable $subdirectory {
        get => str('Factory');
    }
}
