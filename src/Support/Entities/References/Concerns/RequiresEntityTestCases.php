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
    public function it_derives_directory_from_entity(): void
    {
        $expected = $this->expectedSubdirectory
            ? $this->subject->entity->directory.'/'.$this->expectedSubdirectory
            : (string) $this->subject->entity->directory;

        $this->assertSame($expected, (string) $this->subject->directory);
    }
}
