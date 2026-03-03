<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use PHPUnit\Framework\Attributes\CoversClass;
use Support\Entities\References\Concerns\RequiresEntityTestCases;
use Support\Entities\References\Entity;
use Tests\TestCase;
use Tooling\GeneratorCommands\Testing\Concerns\ReferenceTestCases;
use Tooling\GeneratorCommands\Testing\Contracts\TestsReference;

#[CoversClass(Factory::class)]
class FactoryTest extends TestCase implements TestsReference
{
    use ReferenceTestCases;
    use RequiresEntityTestCases;

    public Factory $subject {
        get => new Factory(new Entity(name: 'Post', baseNamespace: 'App\\'));
    }

    public string $expectedName {
        get => 'Factory';
    }

    public string $expectedSubdirectory {
        get => 'Factory';
    }
}
