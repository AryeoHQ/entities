<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\References\Concerns\RequiresEntityTestCases;
use Support\Entities\References\Entity;
use Tests\Support\Entities\References\Contracts\TestsReference;
use Tests\TestCase;

#[CoversClass(Event::class)]
class EventTest extends TestCase implements TestsReference
{
    use RequiresEntityTestCases;

    public Event $subject {
        get => new Event(new Entity(name: 'Post', baseNamespace: 'App\\'), 'creating');
    }

    public string $expectedName {
        get => 'Creating';
    }

    public string $expectedSubdirectory {
        get => 'Events';
    }

    #[Test]
    public function it_stores_the_event_name(): void
    {
        $this->assertSame('creating', $this->subject->event);
    }

    #[Test]
    public function it_capitalizes_the_name(): void
    {
        $this->assertSame('Creating', $this->subject->name->toString());
    }

    #[Test]
    public function it_derives_semantic_name(): void
    {
        $this->assertSame('post.creating', $this->subject->semanticName->toString());
    }

    #[Test]
    public function it_kebab_cases_multi_word_semantic_names(): void
    {
        $event = new Event(new Entity(name: 'Post', baseNamespace: 'App\\'), 'forceDeleted');

        $this->assertSame('post.force-deleted', $event->semanticName->toString());
    }
}
