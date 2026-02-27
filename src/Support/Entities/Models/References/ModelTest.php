<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\References\Concerns\EntityTestCases;
use Support\Entities\References\Contracts\Entity;
use Tests\Support\Entities\References\Contracts\TestsEntity;
use Tests\TestCase;

#[CoversClass(Model::class)]
class ModelTest extends TestCase implements TestsEntity
{
    use EntityTestCases;

    public Model $subject {
        get => new Model(name: 'Post', baseNamespace: 'App\\');
    }

    #[Test]
    public function it_implements_entity_contract(): void
    {
        $this->assertInstanceOf(Entity::class, $this->subject);
    }

    #[Test]
    public function it_provides_a_builder_reference(): void
    {
        $subject = $this->subject;

        $this->assertInstanceOf(Builder::class, $subject->builder);
        $this->assertSame($subject, $subject->builder->entity);
    }

    #[Test]
    public function it_provides_a_collection_reference(): void
    {
        $subject = $this->subject;

        $this->assertInstanceOf(Collection::class, $subject->collection);
        $this->assertSame($subject, $subject->collection->entity);
    }

    #[Test]
    public function it_provides_a_factory_reference(): void
    {
        $subject = $this->subject;

        $this->assertInstanceOf(Factory::class, $subject->factory);
        $this->assertSame($subject, $subject->factory->entity);
    }

    #[Test]
    public function it_creates_an_event_reference(): void
    {
        $subject = $this->subject;
        $event = $subject->event('creating');

        $this->assertInstanceOf(Event::class, $event);
        $this->assertSame($subject, $event->entity);
        $this->assertSame('creating', $event->event);
    }
}
