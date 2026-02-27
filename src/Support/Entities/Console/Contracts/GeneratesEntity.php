<?php

declare(strict_types=1);

namespace Support\Entities\Console\Contracts;

use Illuminate\Support\Stringable;
use Support\Entities\References\Contracts\Entity;
use Support\Entities\References\Contracts\Reference;

interface GeneratesEntity
{
    public string $stub { get; }

    public Stringable $nameInput { get; }

    public Entity $entity { get; }

    public Reference $reference { get; }
}
