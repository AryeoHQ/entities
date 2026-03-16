<?php

declare(strict_types=1);

namespace Support\Entities\Console\Contracts;

use Illuminate\Support\Stringable;
use Support\Entities\References\Entity;
use Tooling\GeneratorCommands\Contracts\GeneratesFile;

interface GeneratesForEntity extends GeneratesFile
{
    public Entity $entity { get; }

    public function resolveEntity(): void;

    public function entityFromPrompt(): Stringable;
}
