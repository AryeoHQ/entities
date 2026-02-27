<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\References\Concerns\RequiresEntityTestCases;
use Support\Entities\References\Entity;
use Tests\Support\Entities\References\Contracts\TestsReference;
use Tests\TestCase;

#[CoversClass(Builder::class)]
class BuilderTest extends TestCase implements TestsReference
{
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

    #[Test]
    public function it_derives_test_name(): void
    {
        $this->assertSame('BuilderTest', $this->subject->test->name->toString());
    }

    #[Test]
    public function it_derives_test_file_path(): void
    {
        $this->assertStringEndsWith(
            $this->subject->directory.'/BuilderTest.php',
            $this->subject->test->filePath->toString(),
        );
    }
}
