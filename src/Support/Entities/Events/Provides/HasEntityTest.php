<?php

declare(strict_types=1);

namespace Support\Entities\Events\Provides;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use ReflectionProperty;
use Support\Entities\Contracts\Entity;
use Support\Entities\Events\IdentifiesEntity\Exceptions\MultipleDefined;
use Support\Entities\Events\IdentifiesEntity\Exceptions\NotDefined;
use Support\Entities\Events\IdentifiesEntity\Exceptions\NotEntity;
use Tests\Fixtures\Support\Posts\Events\Created;
use Tests\Fixtures\Support\Posts\Post;
use Tests\Fixtures\Tooling\Entities\HasEntityWithInvalidIdentifiesEntityType;
use Tests\Fixtures\Tooling\Entities\HasEntityWithMultipleIdentifiesEntityAttributes;
use Tests\Fixtures\Tooling\Entities\HasEntityWithoutIdentifiesEntityAttribute;
use Tests\TestCase;

#[CoversTrait(HasEntity::class)]
class HasEntityTest extends TestCase
{
    #[Test]
    public function it_uses_the_has_entity_trait(): void
    {
        $this->assertContains(
            HasEntity::class,
            collect(
                new ReflectionClass(Created::class)->getTraits()
            )->keys(),
        );
    }

    #[Test]
    public function it_exposes_the_entity(): void
    {
        $post = Post::factory()->make();
        $event = new Created($post);

        $this->assertInstanceOf(Entity::class, $event->entity);
        $this->assertSame($post, $event->entity);
    }

    #[Test]
    public function it_throws_when_no_entity_attribute_is_defined(): void
    {
        $this->expectException(NotDefined::class);

        $event = new HasEntityWithoutIdentifiesEntityAttribute;
        new ReflectionProperty($event, 'entityProperty')->getValue($event);
    }

    #[Test]
    public function it_throws_when_multiple_entity_attributes_are_defined(): void
    {
        $this->expectException(MultipleDefined::class);

        $event = new HasEntityWithMultipleIdentifiesEntityAttributes;
        new ReflectionProperty($event, 'entityProperty')->getValue($event);
    }

    #[Test]
    public function it_throws_when_entity_property_is_not_an_entity(): void
    {
        $this->expectException(NotEntity::class);

        $event = new HasEntityWithInvalidIdentifiesEntityType;
        new ReflectionProperty($event, 'entityProperty')->getValue($event);
    }

    #[Test]
    public function it_would_not_serialize_entity(): void
    {
        $property = new ReflectionProperty(HasEntity::class, 'entity');

        $this->assertTrue(
            $property->isVirtual(),
            '`$entity` is just a convenience accessor and should not be included in serialization.'
        );
    }

    #[Test]
    public function it_would_serialize_entity_property(): void
    {
        $property = new ReflectionProperty(HasEntity::class, 'entityProperty');

        $this->assertFalse(
            $property->isVirtual(),
            '`$entityProperty` should be serialized so the Reflection lookup is not repeated after deserialization.'
        );
    }

    #[Test]
    public function it_always_evaluates_entity_property_lookup(): void
    {
        $event = new Created(Post::factory()->make());
        $property = new ReflectionProperty($event, 'entityProperty');

        $this->assertNotEmpty(
            $property->getValue($event),
        );
    }
}
