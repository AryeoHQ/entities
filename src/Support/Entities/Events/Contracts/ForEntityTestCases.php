<?php

declare(strict_types=1);

namespace Support\Entities\Events\Contracts;

use Illuminate\Support\Stringable;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @mixin TestCase
 */
trait ForEntityTestCases
{
    #[Test]
    public function it_has_an_alias(): void
    {
        $this->assertInstanceOf(Stringable::class, $this->event->alias);
        $this->assertNotEmpty($this->event->alias->toString());
    }

    #[Test]
    public function it_has_a_unique_alias(): void
    {
        $this->assertInstanceOf(Stringable::class, $this->event->uniqueAlias);
        $this->assertNotEmpty($this->event->uniqueAlias->toString());
    }
}
