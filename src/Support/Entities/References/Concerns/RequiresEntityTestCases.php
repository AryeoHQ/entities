<?php

declare(strict_types=1);

namespace Support\Entities\References\Concerns;

use PHPUnit\Framework\Attributes\Test;

/**
 * @mixin \PHPUnit\Framework\TestCase
 */
trait RequiresEntityTestCases
{
    #[Test]
    public function it_derives_namespace_from_entity(): void
    {
        $expected = $this->expectedSubdirectory
            ? $this->subject->entity->namespace.'\\'.$this->expectedSubdirectory
            : (string) $this->subject->entity->namespace;

        $this->assertSame($expected, (string) $this->subject->namespace);
    }

    #[Test]
    public function it_derives_fqcn_from_namespace_and_name(): void
    {
        $this->assertSame(
            $this->subject->namespace.'\\'.$this->expectedName,
            (string) $this->subject->fqcn,
        );
    }

    #[Test]
    public function it_derives_directory_from_entity(): void
    {
        $expected = $this->expectedSubdirectory
            ? $this->subject->entity->directory.'/'.$this->expectedSubdirectory
            : (string) $this->subject->entity->directory;

        $this->assertSame($expected, (string) $this->subject->directory);
    }

    #[Test]
    public function it_derives_file_path_from_directory_and_name(): void
    {
        $this->assertStringEndsWith(
            $this->subject->directory.'/'.$this->expectedName.'.php',
            $this->subject->filePath->toString(),
        );
    }
}
