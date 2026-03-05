<?php

declare(strict_types=1);

namespace Support\Entities\Console\Commands;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Contracts\Entity as EntityContract;
use Tests\Support\Entities\Concerns\ProvidesEntity;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesEntity;
use Tests\TestCase;
use Tooling\GeneratorCommands\References\Contracts\Reference;
use Tooling\GeneratorCommands\Testing\Concerns\GeneratesFileTestCases;
use Tooling\GeneratorCommands\Testing\Concerns\RetrievesNamespaceTestCases;

#[CoversClass(MakeEntity::class)]
class MakeEntityTest extends TestCase implements TestsGeneratesEntity
{
    use GeneratesFileTestCases;
    use InteractsWithPublishedFiles; // @phpstan-ignore-line
    use ProvidesEntity;
    use RetrievesNamespaceTestCases;

    /** @var array<array-key, string> */
    protected array $files {
        get => [
            $this->entity->directory->append('/*')->toString(),
            $this->entity->policy->directory->append('/*')->toString(),
        ];
    }

    public Reference $reference {
        get => $this->entity;
    }

    /** @var array<string, mixed> */
    public array $baselineInput {
        get => ['name' => $this->entity->name->toString(), '--namespace' => 'App\\', '--no-model' => true];
    }

    /** @var array<string, mixed> */
    public array $withNamespaceBackslashInput {
        get => $this->baselineInput;
    }

    /** @var array<string, mixed> */
    public array $withoutNamespaceBackslashInput {
        get => ['name' => $this->entity->name->toString(), '--namespace' => 'App', '--no-model' => true];
    }

    #[Test]
    public function entity_implements_contract(): void
    {
        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $entity = file_get_contents($this->entity->filePath->toString());

        $this->assertStringContainsString('use '.EntityContract::class.';', $entity);
        $this->assertStringContainsString(
            'class '.$this->entity->name.' implements '.class_basename(EntityContract::class),
            $entity
        );
    }

    #[Test]
    public function entity_test_file_is_created(): void
    {
        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();
        $this->assertFileExists($this->entity->test->filePath->toString());
    }

    #[Test]
    public function entity_policy_is_created(): void
    {
        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();
        $this->assertFileExists($this->entity->policy->filePath->toString());
        $this->assertFileExists($this->entity->policy->test->filePath->toString());
    }

    #[Test]
    public function entity_provider_registers_policy_via_gate(): void
    {
        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $this->assertFileExists($this->entity->provider->filePath->toString());

        $serviceProvider = file_get_contents($this->entity->provider->filePath->toString());

        $this->assertStringNotContainsString(class_basename(Relation::class).'::enforceMorphMap', $serviceProvider);
        $this->assertStringContainsString(
            class_basename(Gate::class).'::policy('.$this->entity->name.'::class, Policy::class);',
            $serviceProvider
        );
    }

    #[Test]
    public function model_flag_warns_to_use_make_model(): void
    {
        $this->artisan($this->command, ['name' => $this->entity->name, '--namespace' => 'App\\'])
            ->expectsQuestion('Did you intend to create a model?', true)
            ->expectsOutputToContain('make:model')
            ->assertSuccessful();

        $this->assertFileDoesNotExist($this->entity->filePath->toString());
    }

    #[Test]
    public function model_flag_continues_as_entity_when_declined(): void
    {
        $this->artisan($this->command, ['name' => $this->entity->name, '--namespace' => 'App\\'])
            ->expectsQuestion('Did you intend to create a model?', false)
            ->assertSuccessful();

        $this->assertFileExists($this->entity->filePath->toString());
    }

    #[Test]
    public function no_policy_omits_gate_from_provider(): void
    {
        $this->artisan($this->command, [...$this->baselineInput, '--no-policy' => true])->assertSuccessful();

        $this->assertFileDoesNotExist($this->entity->policy->filePath->toString());

        $provider = file_get_contents($this->entity->provider->filePath->toString());

        $this->assertStringNotContainsString(Gate::class.'::policy', $provider);
        $this->assertStringNotContainsString(Gate::class, $provider);
    }
}
