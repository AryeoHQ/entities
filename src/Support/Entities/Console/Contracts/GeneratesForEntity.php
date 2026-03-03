<?php

declare(strict_types=1);

namespace Support\Entities\Console\Contracts;

use Illuminate\Support\Stringable;
use Support\Entities\References\Contracts\Entity;
use Tooling\GeneratorCommands\Contracts\GeneratesFile;

interface GeneratesForEntity extends GeneratesFile
{
    public Stringable $entityInput { get; }

    public Entity $entity { get; }
}
