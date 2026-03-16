<?php

declare(strict_types=1);

namespace Support\Entities\References;

use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tooling\GeneratorCommands\References\ReferenceTestCases;
use Tooling\GeneratorCommands\Testing\Contracts\TestsReference;

#[CoversClass(Policy::class)]
class PolicyTest extends TestCase implements TestsReference
{
    use ReferenceTestCases;

    public Policy $subject {
        get => new Policy(name: 'Policy', baseNamespace: '\\Workbench\\App\\Entities\\Posts');
    }

    public string $expectedName {
        get => 'Policy';
    }
}
