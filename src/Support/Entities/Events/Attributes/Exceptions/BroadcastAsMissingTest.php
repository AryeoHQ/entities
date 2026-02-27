<?php

declare(strict_types=1);

namespace Support\Entities\Events\Attributes\Exceptions;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

#[CoversClass(BroadcastAsMissing::class)]
class BroadcastAsMissingTest extends TestCase
{
    #[Test]
    public function it_describes_the_missing_attribute(): void
    {
        $exception = BroadcastAsMissing::on('App\Events\Created');

        $this->assertSame(
            'The [App\Events\Created] event must have a #[BroadcastAs] attribute.',
            $exception->getMessage(),
        );
    }

    #[Test]
    public function it_is_a_logic_exception(): void
    {
        $this->assertInstanceOf(RuntimeException::class, BroadcastAsMissing::on('Foo'));
    }
}
