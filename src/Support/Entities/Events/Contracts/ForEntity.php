<?php

declare(strict_types=1);

namespace Support\Entities\Events\Contracts;

use Illuminate\Support\Stringable;
use Support\Entities\Contracts\Entity;

interface ForEntity
{
    public Entity $entity { get; }

    public Stringable $alias { get; }

    public Stringable $uniqueAlias { get; }
}
