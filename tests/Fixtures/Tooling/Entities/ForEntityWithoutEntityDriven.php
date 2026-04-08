<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Illuminate\Support\Stringable;
use Support\Entities\Contracts\Entity;
use Support\Entities\Events\Contracts\ForEntity;

final class ForEntityWithoutEntityDriven implements ForEntity
{
    public readonly Entity $entity;

    public Stringable $alias {
        get => str('test');
    }

    public Stringable $uniqueAlias {
        get => str('test');
    }

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }
}
