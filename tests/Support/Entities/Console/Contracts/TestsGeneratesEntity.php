<?php

declare(strict_types=1);

namespace Tests\Support\Entities\Console\Contracts;

use Support\Entities\References\Entity;
use Tooling\GeneratorCommands\References\Contracts\Reference;

interface TestsGeneratesEntity
{
    /** @var class-string */
    public string $command { get; }

    /** @var array<string, mixed> */
    public array $baselineInput { get; }

    public Entity $entity { get; }

    public Reference $reference { get; }

    /** @var array<string, mixed> */
    public array $withNamespaceBackslashInput { get; }

    /** @var array<string, mixed> */
    public array $withoutNamespaceBackslashInput { get; }
}
