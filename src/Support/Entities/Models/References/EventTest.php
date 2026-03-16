<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tooling\GeneratorCommands\References\ReferenceTestCases;
use Tooling\GeneratorCommands\Testing\Contracts\TestsReference;

#[CoversClass(Event::class)]
class EventTest extends TestCase implements TestsReference
{
    use ReferenceTestCases;

    public Event $subject {
        get => new Event(name: 'Creating', baseNamespace: '\\Workbench\\App\\Entities\\Posts');
    }

    public string $expectedName {
        get => 'Creating';
    }

    #[Test]
    public function it_derives_the_key(): void
    {
        $this->assertSame('creating', $this->subject->key->toString());
    }

    #[Test]
    public function it_derives_semantic_name(): void
    {
        $this->assertSame('post.creating', $this->subject->semanticName->toString());
    }

    #[Test]
    public function it_kebab_cases_multi_word_semantic_names(): void
    {
        $event = new Event(name: 'ForceDeleted', baseNamespace: '\\Workbench\\App\\Entities\\Posts');

        $this->assertSame('post.force-deleted', $event->semanticName->toString());
    }

    #[Test]
    public function it_hydrates_the_model_from_namespace(): void
    {
        $model = $this->subject->model;

        $this->assertInstanceOf(Model::class, $model);
        $this->assertSame('Post', $model->name->toString());
    }
}
