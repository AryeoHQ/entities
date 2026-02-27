<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\References\Concerns\RequiresEntityTestCases;
use Support\Entities\References\Entity;
use Tests\Support\Entities\References\Contracts\TestsReference;
use Tests\TestCase;

#[CoversClass(Collection::class)]
class CollectionTest extends TestCase implements TestsReference
{
    use RequiresEntityTestCases;

    public Collection $subject {
        get => new Collection(new Entity(name: 'Post', baseNamespace: 'App\\'));
    }

    public string $expectedName {
        get => 'Posts';
    }

    public string $expectedSubdirectory {
        get => 'Collection';
    }

    #[Test]
    public function it_derives_name_from_entity_plural(): void
    {
        $this->assertSame('Posts', $this->subject->name->toString());
    }

    #[Test]
    public function it_derives_test_name_from_entity_plural(): void
    {
        $this->assertSame('PostsTest', $this->subject->test->name->toString());
    }

    #[Test]
    public function it_derives_test_file_path(): void
    {
        $this->assertStringEndsWith(
            $this->subject->directory.'/PostsTest.php',
            $this->subject->test->filePath->toString(),
        );
    }
}
