<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\References\Concerns\RequiresEntityTestCases;
use Support\Entities\References\Entity;
use Tests\Support\Entities\References\Contracts\TestsReference;
use Tests\TestCase;

#[CoversClass(Factory::class)]
class FactoryTest extends TestCase implements TestsReference
{
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

    #[Test]
    public function it_derives_test_name(): void
    {
        $this->assertSame('FactoryTest', $this->subject->test->name->toString());
    }

    #[Test]
    public function it_derives_test_file_path(): void
    {
        $this->assertStringEndsWith(
            $this->subject->directory.'/FactoryTest.php',
            $this->subject->test->filePath->toString(),
        );
    }
}
