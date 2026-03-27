<?php

declare(strict_types=1);

namespace Support\Entities\Console\Commands;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\References\Entity;
use Support\Entities\References\Provider;
use Tests\Support\Entities\Concerns\ProvidesEntity;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesForEntity;
use Tests\TestCase;
use Tooling\Composer\Composer;
use Tooling\Entities\Composer\ClassMap\Collectors\Entities;
use Tooling\GeneratorCommands\Testing\Concerns\GeneratesFileTestCases;

#[CoversClass(MakeProvider::class)]
class MakeProviderTest extends TestCase implements TestsGeneratesForEntity
{
    use GeneratesFileTestCases;
    use ProvidesEntity;

    public Provider $reference {
        get => $this->entity->provider;
    }

    /** @var array<string, mixed> */
    public array $baselineInput {
        get => ['entity' => $this->entity->fqcn->toString()];
    }

    #[Test]
    public function it_creates_a_service_provider_for_a_model(): void
    {
        Composer::fake();

        $this->artisan($this->command, [...$this->baselineInput, '--model' => true])->assertSuccessful();

        $this->assertTrue($this->app['files']->exists($this->reference->filePath->toString()));

        $serviceProvider = $this->app['files']->get($this->reference->filePath->toString());

        $this->assertStringContainsString('use '.$this->entity->fqcn->ltrim('\\').';', $serviceProvider);
        $this->assertStringContainsString(class_basename(Relation::class).'::morphMap([', $serviceProvider);
        $this->assertStringContainsString("'".$this->entity->variableName."' => ".$this->entity->name.'::class,', $serviceProvider);
    }

    #[Test]
    public function non_model_provider_registers_policy_via_gate(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $serviceProvider = $this->app['files']->get($this->reference->filePath->toString());

        $this->assertStringNotContainsString(
            class_basename(Relation::class).'::morphMap',
            $serviceProvider
        );
        $this->assertStringContainsString('use '.Gate::class.';', $serviceProvider);
        $this->assertStringContainsString('use '.$this->entity->fqcn->ltrim('\\').';', $serviceProvider);
        $this->assertStringContainsString('use '.$this->entity->policy->fqcn->ltrim('\\').';', $serviceProvider);
        $this->assertStringContainsString(
            class_basename(Gate::class).'::policy('.$this->entity->name.'::class, Policy::class);',
            $serviceProvider
        );
    }

    #[Test]
    public function it_prompts_for_entity_when_argument_is_omitted(): void
    {
        $target = new Entity('PromptTarget', 'App\\');

        Composer::fake();
        Entities::fake([$target->fqcn->ltrim('\\')->toString()]);

        $this->artisan($this->command)
            ->expectsSearch('Which entity?', $target->fqcn->ltrim('\\')->toString(), 'PromptTarget', [$target->fqcn->ltrim('\\')->toString()])
            ->assertSuccessful();
    }

    #[Test]
    public function it_warns_and_prompts_when_entity_is_not_fully_qualified(): void
    {
        $target = new Entity('PromptTarget', 'App\\');

        Composer::fake();
        Entities::fake([$target->fqcn->ltrim('\\')->toString()]);

        $this->artisan($this->command, ['entity' => 'PromptTarget'])
            ->expectsOutputToContain('fully-qualified class name')
            ->expectsSearch('Which entity?', $target->fqcn->ltrim('\\')->toString(), 'PromptTarget', [$target->fqcn->ltrim('\\')->toString()])
            ->assertSuccessful();
    }
}
