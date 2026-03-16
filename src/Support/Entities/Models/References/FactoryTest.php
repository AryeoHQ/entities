<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tooling\GeneratorCommands\References\ReferenceTestCases;
use Tooling\GeneratorCommands\Testing\Contracts\TestsReference;

#[CoversClass(Factory::class)]
class FactoryTest extends TestCase implements TestsReference
{
    use ReferenceTestCases;

    public Factory $subject {
        get => new Factory(name: 'Factory', baseNamespace: '\\Workbench\\App\\Entities\\Posts');
    }

    public string $expectedName {
        get => 'Factory';
    }
}
