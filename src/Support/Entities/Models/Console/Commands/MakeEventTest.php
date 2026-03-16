<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Events\Attributes\BroadcastAs;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Provides\EntityDriven;
use Support\Entities\Models\References\Event;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesForEntity;
use Tests\Support\Entities\Models\Concerns\ProvidesModel;
use Tests\TestCase;
use Tooling\GeneratorCommands\Testing\Concerns\CleansUpGeneratorCommands;
use Tooling\GeneratorCommands\Testing\Concerns\GeneratesFileTestCases;

#[CoversClass(MakeEvent::class)]
class MakeEventTest extends TestCase implements TestsGeneratesForEntity
{
    use CleansUpGeneratorCommands;
    use GeneratesFileTestCases;
    use ProvidesModel;

    /** @var array<array-key, string> */
    protected array $files {
        get => [
            $this->entity->event('Created')->directory->append('/*')->toString(),
        ];
    }

    public Event $reference {
        get => $this->entity->event('Created');
    }

    /** @var array<string, mixed> */
    public array $baselineInput {
        get => ['name' => 'Created', '--entity' => $this->entity->fqcn->toString()];
    }

    #[Test]
    public function it_generates_an_event_with_the_broadcast_as_attribute(): void
    {
        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $contents = file_get_contents($this->reference->filePath->toString());

        $this->assertStringContainsString('#['.class_basename(BroadcastAs::class)."('".$this->entity->variableName.".created')]", $contents);
    }

    #[Test]
    public function it_generates_an_event_that_uses_the_entity_driven_trait(): void
    {
        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $contents = file_get_contents($this->reference->filePath->toString());

        $this->assertStringContainsString('use '.class_basename(EntityDriven::class).';', $contents);
    }

    #[Test]
    public function it_generates_an_event_that_implements_for_entity(): void
    {
        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $contents = file_get_contents($this->reference->filePath->toString());

        $this->assertStringContainsString('implements '.class_basename(ForEntity::class), $contents);
    }

    #[Test]
    public function it_generates_an_event_that_accepts_the_entity_model(): void
    {
        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $contents = file_get_contents($this->reference->filePath->toString());

        $this->assertStringContainsString('public readonly '.$this->entity->name.' $entity', $contents);
    }

    #[Test]
    public function it_kebab_cases_multi_word_semantic_event_names(): void
    {
        $this->artisan($this->command, ['name' => 'ForceDeleted', '--entity' => $this->entity->fqcn->toString()])->assertSuccessful();

        $contents = file_get_contents($this->entity->event('ForceDeleted')->filePath->toString());

        $this->assertStringContainsString('#['.class_basename(BroadcastAs::class)."('".$this->entity->variableName.".force-deleted')]", $contents);
    }
}
