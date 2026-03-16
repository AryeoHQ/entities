<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tooling\GeneratorCommands\References\ReferenceTestCases;
use Tooling\GeneratorCommands\Testing\Contracts\TestsReference;

#[CoversClass(Collection::class)]
class CollectionTest extends TestCase implements TestsReference
{
    use ReferenceTestCases;

    public Collection $subject {
        get => new Collection(name: 'Posts', baseNamespace: '\\Workbench\\App\\Entities\\Posts');
    }

    public string $expectedName {
        get => 'Posts';
    }
}
