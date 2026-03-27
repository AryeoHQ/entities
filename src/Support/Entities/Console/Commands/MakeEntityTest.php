<?php

declare(strict_types=1);

namespace Support\Entities\Console\Commands;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Contracts\Entity as EntityContract;
use Support\Entities\References\Entity;
use Tests\Support\Entities\Concerns\ProvidesEntity;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesEntity;
use Tests\TestCase;
use Tooling\Composer\Composer;
use Tooling\GeneratorCommands\Testing\Concerns\GeneratesFileTestCases;
use Tooling\GeneratorCommands\Testing\Concerns\RetrievesNamespaceTestCases;

#[CoversClass(MakeEntity::class)]
class MakeEntityTest extends TestCase implements TestsGeneratesEntity
{
    use GeneratesFileTestCases;
    use ProvidesEntity;
    use RetrievesNamespaceTestCases;

    public Entity $reference {
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

    private Entity $nestedEntity {
        get => new Entity(class_basename(static::class), 'App\\Nested\\Deeper\\');
    }

    /** @var array<string, mixed> */
    public array $withNestedNamespaceInput {
        get => ['name' => $this->entity->name->toString(), '--namespace' => 'App\\Nested\\Deeper', '--no-model' => true];
    }

    protected string $expectedNestedFilePath {
        get => $this->nestedEntity->filePath->toString();
    }

    #[Test]
    public function entity_implements_contract(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $entity = $this->app['files']->get($this->entity->filePath->toString());

        $this->assertStringContainsString('use '.EntityContract::class.';', $entity);
        $this->assertStringContainsString(
            'class '.$this->entity->name.' implements '.class_basename(EntityContract::class),
            $entity
        );
    }

    #[Test]
    public function entity_test_file_is_created(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();
        $this->assertTrue($this->app['files']->exists($this->entity->test->filePath->toString()));
    }

    #[Test]
    public function entity_policy_is_created(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();
        $this->assertTrue($this->app['files']->exists($this->entity->policy->filePath->toString()));
        $this->assertTrue($this->app['files']->exists($this->entity->policy->test->filePath->toString()));
    }

    #[Test]
    public function entity_provider_registers_policy_via_gate(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $this->assertTrue($this->app['files']->exists($this->entity->provider->filePath->toString()));

        $serviceProvider = $this->app['files']->get($this->entity->provider->filePath->toString());

        $this->assertStringNotContainsString(class_basename(Relation::class).'::morphMap', $serviceProvider);
        $this->assertStringContainsString(
            class_basename(Gate::class).'::policy('.$this->entity->name.'::class, Policy::class);',
            $serviceProvider
        );
    }

    #[Test]
    public function model_flag_warns_to_use_make_model(): void
    {
        Composer::fake();

        $this->artisan($this->command, ['name' => $this->entity->name, '--namespace' => 'App\\'])
            ->expectsQuestion('Did you intend to create a model?', true)
            ->expectsOutputToContain('make:model')
            ->assertSuccessful();

        $this->assertFalse($this->app['files']->exists($this->entity->filePath->toString()));
    }

    #[Test]
    public function model_flag_continues_as_entity_when_declined(): void
    {
        Composer::fake();

        $this->artisan($this->command, ['name' => $this->entity->name, '--namespace' => 'App\\'])
            ->expectsQuestion('Did you intend to create a model?', false)
            ->assertSuccessful();

        $this->assertTrue($this->app['files']->exists($this->entity->filePath->toString()));
    }

    #[Test]
    public function no_policy_omits_gate_from_provider(): void
    {
        Composer::fake();

        $this->artisan($this->command, [...$this->baselineInput, '--no-policy' => true])->assertSuccessful();

        $this->assertFalse($this->app['files']->exists($this->entity->policy->filePath->toString()));

        $provider = $this->app['files']->get($this->entity->provider->filePath->toString());

        $this->assertStringNotContainsString(Gate::class.'::policy', $provider);
        $this->assertStringNotContainsString(Gate::class, $provider);
    }
}
