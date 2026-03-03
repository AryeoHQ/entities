<?php

declare(strict_types=1);

namespace Support\Entities\References;

use PHPUnit\Framework\Attributes\CoversClass;
use Support\Entities\References\Concerns\RequiresEntityTestCases;
use Tests\TestCase;
use Tooling\GeneratorCommands\Testing\Concerns\ReferenceTestCases;
use Tooling\GeneratorCommands\Testing\Contracts\TestsReference;

#[CoversClass(Provider::class)]
class ProviderTest extends TestCase implements TestsReference
{
    use ReferenceTestCases;
    use RequiresEntityTestCases;

    public Provider $subject {
        get => new Provider(new Entity(name: 'Post', baseNamespace: 'App\\'));
    }

    public string $expectedName {
        get => 'Provider';
    }

    public null|string $expectedSubdirectory {
        get => null;
    }
}
