<?php

declare(strict_types=1);

namespace Support\Entities\Console\Contracts;

use Support\Entities\References\Contracts\Entity;
use Tooling\GeneratorCommands\Contracts\GeneratesFile;

interface GeneratesEntity extends GeneratesFile
{
    public Entity $entity { get; }
}
