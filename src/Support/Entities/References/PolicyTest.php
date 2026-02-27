<?php

declare(strict_types=1);

namespace Support\Entities\References;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\References\Concerns\RequiresEntityTestCases;
use Tests\Support\Entities\References\Contracts\TestsReference;
use Tests\TestCase;

#[CoversClass(Policy::class)]
class PolicyTest extends TestCase implements TestsReference
{
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

    #[Test]
    public function it_derives_test_name(): void
    {
        $this->assertSame('PolicyTest', $this->subject->test->name->toString());
    }

    #[Test]
    public function it_derives_test_file_path(): void
    {
        $this->assertStringEndsWith(
            $this->subject->directory.'/PolicyTest.php',
            $this->subject->test->filePath->toString(),
        );
    }
}
