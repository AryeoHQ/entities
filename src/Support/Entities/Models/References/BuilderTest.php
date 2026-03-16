<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tooling\GeneratorCommands\References\ReferenceTestCases;
use Tooling\GeneratorCommands\Testing\Contracts\TestsReference;

#[CoversClass(Builder::class)]
class BuilderTest extends TestCase implements TestsReference
{
    use ReferenceTestCases;

    public Builder $subject {
        get => new Builder(name: 'Builder', baseNamespace: '\\Workbench\\App\\Entities\\Posts');
    }

    public string $expectedName {
        get => 'Builder';
    }
}
