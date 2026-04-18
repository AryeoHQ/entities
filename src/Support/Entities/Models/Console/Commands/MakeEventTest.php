<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Entity\IdentifiesEntity;
use Support\Entities\Events\Provides\HasEntity;
use Support\Entities\Models\References\Event;
use Support\Entities\Models\References\Model;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesForEntity;
use Tests\Support\Entities\Models\Concerns\ProvidesModel;
use Tests\TestCase;
use Tooling\Composer\Composer;
use Tooling\Entities\Composer\ClassMap\Collectors\Models;
use Tooling\GeneratorCommands\Testing\Concerns\GeneratesFileTestCases;

#[CoversClass(MakeEvent::class)]
class MakeEventTest extends TestCase implements TestsGeneratesForEntity
{
    use GeneratesFileTestCases;
    use ProvidesModel;

    public Event $reference {
        get => $this->entity->event('Created');
    }

    /** @var array<string, mixed> */
    public array $baselineInput {
        get => ['name' => 'Created', '--entity' => $this->entity->fqcn->toString()];
    }

    #[Test]
    public function it_generates_an_event_with_the_identifies_entity_attribute(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $contents = File::get($this->reference->filePath->toString());

        $this->assertStringContainsString('#['.class_basename(IdentifiesEntity::class).']', $contents);
    }

    #[Test]
    public function it_generates_an_event_that_uses_dispatchable(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $contents = File::get($this->reference->filePath->toString());

        $this->assertStringContainsString('use '.class_basename(Dispatchable::class).';', $contents);
    }

    #[Test]
    public function it_generates_an_event_that_uses_has_entity(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $contents = File::get($this->reference->filePath->toString());

        $this->assertStringContainsString('use '.class_basename(HasEntity::class).';', $contents);
    }

    #[Test]
    public function it_generates_an_event_that_implements_for_entity(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $contents = File::get($this->reference->filePath->toString());

        $this->assertStringContainsString('implements '.class_basename(ForEntity::class), $contents);
    }

    #[Test]
    public function it_generates_an_event_that_accepts_the_entity_model(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $contents = File::get($this->reference->filePath->toString());

        $this->assertStringContainsString('public readonly '.$this->entity->name.' $'.$this->entity->variableName, $contents);
    }

    #[Test]
    public function it_prompts_for_entity_when_option_is_omitted(): void
    {
        Composer::fake();

        $target = tap(
            new Model('ModelPromptTarget', 'App\\'),
            fn (Model $entity) => Models::fake([$entity->fqcn->ltrim('\\')->toString()])
        );

        $this->artisan($this->command, ['name' => 'Created'])
            ->expectsSearch('Which entity?', $target->fqcn->ltrim('\\')->toString(), 'ModelPromptTarget', [$target->fqcn->ltrim('\\')->toString()])
            ->assertSuccessful();
    }

    #[Test]
    public function it_warns_and_prompts_when_entity_option_is_not_fully_qualified(): void
    {
        Composer::fake();

        $target = tap(
            new Model('ModelPromptTarget', 'App\\'),
            fn (Model $entity) => Models::fake([$entity->fqcn->ltrim('\\')->toString()])
        );

        $this->artisan($this->command, ['name' => 'Created', '--entity' => 'ModelPromptTarget'])
            ->expectsOutputToContain('fully-qualified class name')
            ->expectsSearch('Which entity?', $target->fqcn->ltrim('\\')->toString(), 'ModelPromptTarget', [$target->fqcn->ltrim('\\')->toString()])
            ->assertSuccessful();
    }
}
