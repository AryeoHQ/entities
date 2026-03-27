<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use Illuminate\Database\Eloquent\Builder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Database\Eloquent\Contracts\Filterable;
use Support\Entities\Models\References\Model;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesForEntity;
use Tests\Support\Entities\Models\Concerns\ProvidesModel;
use Tests\TestCase;
use Tooling\Composer\Composer;
use Tooling\Entities\Composer\ClassMap\Collectors\Models;
use Tooling\GeneratorCommands\Testing\Concerns\GeneratesFileTestCases;

#[CoversClass(MakeBuilder::class)]
class MakeBuilderTest extends TestCase implements TestsGeneratesForEntity
{
    use GeneratesFileTestCases;
    use ProvidesModel;

    public \Support\Entities\Models\References\Builder $reference {
        get => $this->entity->builder;
    }

    /** @var array<string, mixed> */
    public array $baselineInput {
        get => ['entity' => $this->entity->fqcn->toString()];
    }

    #[Test]
    public function it_creates_a_builder_for_an_entity(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $this->assertTrue($this->app['files']->exists($this->reference->filePath->toString()));

        $builder = $this->app['files']->get($this->reference->filePath->toString());

        $this->assertStringContainsString('extends \\'.Builder::class, $builder);
        $this->assertStringContainsString('implements '.class_basename(Filterable::class), $builder);

        $this->assertTrue($this->app['files']->exists($this->reference->test->filePath->toString()));
    }

    #[Test]
    public function it_prompts_for_entity_when_argument_is_omitted(): void
    {
        $target = new Model('ModelPromptTarget', 'App\\');

        Composer::fake();
        Models::fake([$target->fqcn->ltrim('\\')->toString()]);

        $this->artisan($this->command)
            ->expectsSearch('Which entity?', $target->fqcn->ltrim('\\')->toString(), 'ModelPromptTarget', [$target->fqcn->ltrim('\\')->toString()])
            ->assertSuccessful();
    }

    #[Test]
    public function it_warns_and_prompts_when_entity_is_not_fully_qualified(): void
    {
        $target = new Model('ModelPromptTarget', 'App\\');

        Composer::fake();
        Models::fake([$target->fqcn->ltrim('\\')->toString()]);

        $this->artisan($this->command, ['entity' => 'ModelPromptTarget'])
            ->expectsOutputToContain('fully-qualified class name')
            ->expectsSearch('Which entity?', $target->fqcn->ltrim('\\')->toString(), 'ModelPromptTarget', [$target->fqcn->ltrim('\\')->toString()])
            ->assertSuccessful();
    }
}
