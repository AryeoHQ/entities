<?php

declare(strict_types=1);

namespace Support\Entities\References;

use PHPUnit\Framework\Attributes\CoversClass;
use Support\Entities\References\Concerns\RequiresEntityTestCases;
use Tests\Support\Entities\References\Contracts\TestsReference;
use Tests\TestCase;

#[CoversClass(Provider::class)]
class ProviderTest extends TestCase implements TestsReference
{
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
