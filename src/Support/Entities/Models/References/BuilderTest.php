<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use PHPUnit\Framework\Attributes\CoversClass;
use Support\Entities\References\Concerns\RequiresEntityTestCases;
use Support\Entities\References\Entity;
use Tests\TestCase;
use Tooling\GeneratorCommands\Testing\Concerns\ReferenceTestCases;
use Tooling\GeneratorCommands\Testing\Contracts\TestsReference;

#[CoversClass(Builder::class)]
class BuilderTest extends TestCase implements TestsReference
{
    use ReferenceTestCases;
    use RequiresEntityTestCases;

    public Builder $subject {
        get => new Builder(new Entity(name: 'Post', baseNamespace: 'App\\'));
    }

    public string $expectedName {
        get => 'Builder';
    }

    public string $expectedSubdirectory {
        get => 'Builder';
    }
}
