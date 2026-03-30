<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Models\References\Model;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesForEntity;
use Tests\Support\Entities\Models\Concerns\ProvidesModel;
use Tests\TestCase;
use Tooling\Composer\Composer;
use Tooling\Entities\Composer\ClassMap\Collectors\Models;
use Tooling\GeneratorCommands\Testing\Concerns\GeneratesFileTestCases;

#[CoversClass(MakeCollection::class)]
class MakeCollectionTest extends TestCase implements TestsGeneratesForEntity
{
    use GeneratesFileTestCases;
    use ProvidesModel;

    public \Support\Entities\Models\References\Collection $reference {
        get => $this->entity->collection;
    }

    /** @var array<string, mixed> */
    public array $baselineInput {
        get => ['entity' => $this->entity->fqcn->toString()];
    }

    #[Test]
    public function it_creates_a_collection_for_an_entity(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $this->assertTrue(File::exists($this->reference->filePath->toString()));

        $collection = File::get($this->reference->filePath->toString());

        $this->assertStringContainsString('use '.Collection::class.';', $collection);
        $this->assertStringContainsString('@extends '.class_basename(Collection::class).'<int, '.$this->entity->fqcn.'>', $collection);
        $this->assertStringContainsString('class '.$this->reference->name.' extends '.class_basename(Collection::class), $collection);

        $this->assertTrue(File::exists($this->reference->test->filePath->toString()));
    }

    #[Test]
    public function it_prompts_for_entity_when_argument_is_omitted(): void
    {
        Composer::fake();

        $target = tap(
            new Model('ModelPromptTarget', 'App\\'),
            fn (Model $entity) => Models::fake([$entity->fqcn->ltrim('\\')->toString()])
        );

        $this->artisan($this->command)
            ->expectsSearch('Which entity?', $target->fqcn->ltrim('\\')->toString(), 'ModelPromptTarget', [$target->fqcn->ltrim('\\')->toString()])
            ->assertSuccessful();
    }

    #[Test]
    public function it_warns_and_prompts_when_entity_is_not_fully_qualified(): void
    {
        Composer::fake();

        $target = tap(
            new Model('ModelPromptTarget', 'App\\'),
            fn (Model $entity) => Models::fake([$entity->fqcn->ltrim('\\')->toString()])
        );

        $this->artisan($this->command, ['entity' => 'ModelPromptTarget'])
            ->expectsOutputToContain('fully-qualified class name')
            ->expectsSearch('Which entity?', $target->fqcn->ltrim('\\')->toString(), 'ModelPromptTarget', [$target->fqcn->ltrim('\\')->toString()])
            ->assertSuccessful();
    }
}
