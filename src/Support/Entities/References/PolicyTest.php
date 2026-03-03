<?php

declare(strict_types=1);

namespace Support\Entities\References;

use PHPUnit\Framework\Attributes\CoversClass;
use Support\Entities\References\Concerns\RequiresEntityTestCases;
use Tests\TestCase;
use Tooling\GeneratorCommands\Testing\Concerns\ReferenceTestCases;
use Tooling\GeneratorCommands\Testing\Contracts\TestsReference;

#[CoversClass(Policy::class)]
class PolicyTest extends TestCase implements TestsReference
{
    use ReferenceTestCases;
    use RequiresEntityTestCases;

    public Policy $subject {
        get => new Policy(new Entity(name: 'Post', baseNamespace: 'App\\'));
    }

    public string $expectedName {
        get => 'Policy';
    }

    public string $expectedSubdirectory {
        get => 'Policy';
    }
}
