<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\References\Concerns\EntityTestCases;
use Support\Entities\References\Entity;
use Tests\Support\Entities\References\Contracts\TestsEntity;
use Tests\TestCase;

#[CoversClass(Model::class)]
class ModelTest extends TestCase implements TestsEntity
{
    use EntityTestCases;

    public Model $subject {
        get => new Model(name: 'Post', baseNamespace: 'Workbench\\App\\');
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $this->assertInstanceOf(Entity::class, $this->subject);
    }

    #[Test]
    public function it_provides_a_builder_reference(): void
    {
        $this->assertInstanceOf(Builder::class, $this->subject->builder);
    }

    #[Test]
    public function it_provides_a_collection_reference(): void
    {
        $this->assertInstanceOf(Collection::class, $this->subject->collection);
    }

    #[Test]
    public function it_provides_a_factory_reference(): void
    {
        $this->assertInstanceOf(Factory::class, $this->subject->factory);
    }

    #[Test]
    public function it_creates_an_event_reference(): void
    {
        $event = $this->subject->event('creating');

        $this->assertInstanceOf(Event::class, $event);
        $this->assertSame('Creating', $event->name->toString());
    }
}
