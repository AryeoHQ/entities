<?php

declare(strict_types=1);

namespace Support\Entities\Events\Contracts;

use Support\Entities\Contracts\Entity;

interface ForEntity
{
    public Entity $entity { get; }
}
