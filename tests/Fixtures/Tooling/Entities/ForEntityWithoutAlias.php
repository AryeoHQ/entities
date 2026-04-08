<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Support\Entities\Contracts\Entity;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Provides\EntityDriven;

#[\AllowDynamicProperties]
final class ForEntityWithoutAlias implements ForEntity
{
    use EntityDriven;

    public readonly Entity $entity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }
}
