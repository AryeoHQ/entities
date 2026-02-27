<?php

declare(strict_types=1);

namespace Support\Entities\Events\Provides;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Support\Entities\Contracts\Entity;
use Support\Entities\Events\Attributes\BroadcastAs;
use Support\Entities\Events\Attributes\Exceptions\BroadcastAsMissing;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Contracts\ForEntityTestCases;
use Tests\Fixtures\Support\Posts\Events\Created;
use Tests\Fixtures\Support\Posts\Post;
use Tests\Fixtures\Tooling\Entities\ForEntityWithoutBroadcastAs;
use Tests\Support\Entities\Events\Contracts\TestsForEntity;
use Tests\TestCase;

/**
 * @mixin \PHPUnit\Framework\TestCase
 */
#[CoversTrait(EntityDriven::class)]
class EntityDrivenTest extends TestCase implements TestsForEntity
{
    use ForEntityTestCases;

    public ForEntity $event {
        get => new Created(Post::factory()->make());
    }

    #[Test]
    public function it_uses_the_entity_driven_trait(): void
    {
        $this->assertContains(
            EntityDriven::class,
            collect(
                new ReflectionClass(Created::class)->getTraits()
            )->keys(),
        );
    }

    #[Test]
    public function it_exposes_the_entity(): void
    {
        $event = new Created(Post::factory()->make());

        $this->assertInstanceOf(Entity::class, $event->entity);
    }

    #[Test]
    public function it_derives_name(): void
    {
        $event = new Created(Post::factory()->make());

        $expected = (new ReflectionClass($event))
            ->getAttributes(BroadcastAs::class)[0]
            ->newInstance()
            ->name;

        $this->assertSame($expected, $event->broadcastAs());
    }

    #[Test]
    public function it_derives_unique_name_by_interpolating_entity_id(): void
    {
        $post = Post::factory()->make();
        $event = new Created($post);

        $reflection = new ReflectionClass($event);
        $property = $reflection->getProperty('uniqueName');

        $this->assertSame(
            "post.{$post->id}.created",
            $property->getValue($event),
        );
    }

    #[Test]
    public function it_throws_when_broadcast_as_attribute_is_missing(): void
    {
        /** @var Post $post */
        $post = Post::factory()->make();
        $event = new ForEntityWithoutBroadcastAs($post);

        $this->expectException(BroadcastAsMissing::class);

        $event->broadcastAs();
    }
}
