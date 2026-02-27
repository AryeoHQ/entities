<?php

declare(strict_types=1);

namespace Support\Entities\Events\Attributes;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Tests\TestCase;

#[CoversClass(BroadcastAs::class)]
class BroadcastAsTest extends TestCase
{
    #[Test]
    public function it_stores_the_broadcast_name(): void
    {
        $attribute = new BroadcastAs('post.created');

        $this->assertSame('post.created', $attribute->name);
    }

    #[Test]
    public function it_targets_classes(): void
    {
        $reflection = new ReflectionClass(BroadcastAs::class);
        $attributes = $reflection->getAttributes(\Attribute::class);

        $this->assertNotEmpty($attributes);
    }
}
