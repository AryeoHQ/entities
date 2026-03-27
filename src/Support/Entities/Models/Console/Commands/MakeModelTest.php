<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\Pivot;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Contracts\Entity as EntityContract;
use Support\Entities\Models\References\Model;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesEntity;
use Tests\Support\Entities\Models\Concerns\ProvidesModel;
use Tests\TestCase;
use Tooling\Composer\Composer;
use Tooling\GeneratorCommands\Testing\Concerns\GeneratesFileTestCases;
use Tooling\GeneratorCommands\Testing\Concerns\RetrievesNamespaceTestCases;

#[CoversClass(MakeModel::class)]
class MakeModelTest extends TestCase implements TestsGeneratesEntity
{
    use GeneratesFileTestCases;
    use ProvidesModel;
    use RetrievesNamespaceTestCases;

    public Model $reference {
        get => $this->entity;
    }

    /** @var array<string, mixed> */
    public array $baselineInput {
        get => ['name' => $this->entity->name->toString(), '--namespace' => 'App\\'];
    }

    /** @var array<string, mixed> */
    public array $withNamespaceBackslashInput {
        get => $this->baselineInput;
    }

    /** @var array<string, mixed> */
    public array $withoutNamespaceBackslashInput {
        get => ['name' => $this->entity->name->toString(), '--namespace' => 'App'];
    }

    private Model $nestedEntity {
        get => new Model(class_basename(static::class), 'App\\Nested\\Deeper\\');
    }

    /** @var array<string, mixed> */
    public array $withNestedNamespaceInput {
        get => ['name' => $this->entity->name->toString(), '--namespace' => 'App\\Nested\\Deeper'];
    }

    protected string $expectedNestedFilePath {
        get => $this->nestedEntity->filePath->toString();
    }

    #[Test]
    public function model_related_files_are_created(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)
            ->assertSuccessful();

        $this->assertTrue($this->app['files']->exists($this->entity->filePath->toString()));
        $this->assertTrue($this->app['files']->exists($this->entity->factory->filePath->toString()));
        $this->assertTrue($this->app['files']->exists($this->entity->builder->filePath->toString()));
        $this->assertTrue($this->app['files']->exists($this->entity->provider->filePath->toString()));
        $this->assertTrue($this->app['files']->exists($this->entity->policy->filePath->toString()));
        $this->assertTrue($this->app['files']->exists($this->entity->policy->test->filePath->toString()));
        $this->assertTrue($this->app['files']->exists($this->entity->test->filePath->toString()));
        $this->assertTrue($this->app['files']->exists($this->entity->collection->filePath->toString()));
        $this->assertTrue($this->app['files']->exists($this->entity->factory->test->filePath->toString()));
        $this->assertTrue($this->app['files']->exists($this->entity->builder->test->filePath->toString()));
        $this->assertTrue($this->app['files']->exists($this->entity->collection->test->filePath->toString()));
    }

    #[Test]
    public function model_contains_the_correct_attributes(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)
            ->assertSuccessful();

        $model = $this->app['files']->get($this->entity->filePath->toString());

        $this->assertStringContainsString('use '.HasUuids::class.';', $model);
        $this->assertStringContainsString('use '.class_basename(HasUuids::class).';', $model);

        $this->assertStringContainsString('use '.UseFactory::class.';', $model);
        $this->assertStringContainsString('#['.class_basename(UseFactory::class).'('.$this->entity->factory->name.'::class)]', $model);

        $this->assertStringContainsString('use '.UseEloquentBuilder::class.';', $model);
        $this->assertStringContainsString('#['.class_basename(UseEloquentBuilder::class).'('.$this->entity->builder->name.'::class)]', $model);

        $this->assertStringContainsString('use '.CollectedBy::class.';', $model);
        $this->assertStringContainsString('#['.class_basename(CollectedBy::class).'('.$this->entity->collection->name.'::class)]', $model);

        $this->assertStringContainsString('use '.UsePolicy::class.';', $model);
        $this->assertStringContainsString('#['.class_basename(UsePolicy::class).'('.$this->entity->policy->name.'::class)]', $model);

        $this->assertStringContainsString('use '.EntityContract::class.';', $model);
        $this->assertStringContainsString('implements '.class_basename(EntityContract::class), $model);

        $this->assertStringContainsString('use '.EloquentModel::class.';', $model);
        $this->assertStringContainsString('extends '.class_basename(EloquentModel::class).' implements', $model);
    }

    #[Test]
    public function model_creates_and_registers_semantic_events(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)
            ->assertSuccessful();

        $model = $this->app['files']->get($this->entity->filePath->toString());

        $this->assertStringContainsString('protected $dispatchesEvents = [', $model);

        foreach ((new class // @phpstan-ignore class.missingExtends
        {
            use HasEvents;
        })->getObservableEvents() as $event) {
            $ref = $this->entity->event($event);
            $this->assertStringContainsString("'{$event}' => {$ref->subNamespace}\\{$ref->name}::class,", $model);
            $this->assertTrue($this->app['files']->exists($ref->filePath->toString()));
        }
    }

    #[Test]
    public function no_factory_omits_factory_from_model(): void
    {
        Composer::fake();

        $this->artisan($this->command, [...$this->baselineInput, '--no-factory' => true])
            ->assertSuccessful();

        $model = $this->app['files']->get($this->entity->filePath->toString());

        $this->assertStringNotContainsString('UseFactory', $model);
        $this->assertStringNotContainsString('HasFactory', $model);
        $this->assertStringNotContainsString('Factory', $model);

        $this->assertFalse($this->app['files']->exists($this->entity->factory->filePath->toString()));
        $this->assertFalse($this->app['files']->exists($this->entity->factory->test->filePath->toString()));
    }

    #[Test]
    public function no_builder_omits_builder_from_model(): void
    {
        Composer::fake();

        $this->artisan($this->command, [...$this->baselineInput, '--no-builder' => true])
            ->assertSuccessful();

        $model = $this->app['files']->get($this->entity->filePath->toString());

        $this->assertStringNotContainsString('UseEloquentBuilder', $model);
        $this->assertStringNotContainsString('Builder', $model);

        $this->assertFalse($this->app['files']->exists($this->entity->builder->filePath->toString()));
        $this->assertFalse($this->app['files']->exists($this->entity->builder->test->filePath->toString()));
    }

    #[Test]
    public function no_collection_omits_collection_from_model(): void
    {
        Composer::fake();

        $this->artisan($this->command, [...$this->baselineInput, '--no-collection' => true])
            ->assertSuccessful();

        $model = $this->app['files']->get($this->entity->filePath->toString());

        $this->assertStringNotContainsString('CollectedBy', $model);
        $this->assertStringNotContainsString('Collection\\'.$this->entity->plural, $model);

        $this->assertFalse($this->app['files']->exists($this->entity->collection->filePath->toString()));
        $this->assertFalse($this->app['files']->exists($this->entity->collection->test->filePath->toString()));
    }

    #[Test]
    public function no_events_omits_events_from_model(): void
    {
        Composer::fake();

        $this->artisan($this->command, [...$this->baselineInput, '--no-events' => true])
            ->assertSuccessful();

        $model = $this->app['files']->get($this->entity->filePath->toString());

        $this->assertStringNotContainsString('Events', $model);
        $this->assertStringNotContainsString('$dispatchesEvents', $model);

        foreach ((new class // @phpstan-ignore class.missingExtends
        {
            use HasEvents;
        })->getObservableEvents() as $event) {
            $this->assertFalse($this->app['files']->exists($this->entity->event($event)->filePath->toString()));
        }
    }

    #[Test]
    public function no_policy_omits_policy_from_model(): void
    {
        Composer::fake();

        $this->artisan($this->command, [...$this->baselineInput, '--no-policy' => true])
            ->assertSuccessful();

        $model = $this->app['files']->get($this->entity->filePath->toString());

        $this->assertStringNotContainsString('UsePolicy', $model);
        $this->assertStringNotContainsString('Policy', $model);

        $this->assertFalse($this->app['files']->exists($this->entity->policy->filePath->toString()));
        $this->assertFalse($this->app['files']->exists($this->entity->policy->test->filePath->toString()));
    }

    #[Test]
    public function no_test_skips_test_file(): void
    {
        Composer::fake();

        $this->artisan($this->command, [...$this->baselineInput, '--no-test' => true])
            ->assertSuccessful();

        $this->assertTrue($this->app['files']->exists($this->entity->filePath->toString()));
        $this->assertFalse($this->app['files']->exists($this->entity->test->filePath->toString()));
    }

    #[Test]
    public function pivot_model_extends_pivot(): void
    {
        Composer::fake();

        $this->artisan($this->command, [...$this->baselineInput, '--pivot' => true])
            ->assertSuccessful();

        $model = $this->app['files']->get($this->entity->filePath->toString());

        $this->assertStringContainsString('use '.Pivot::class.';', $model);
        $this->assertStringContainsString('extends '.class_basename(Pivot::class).' implements', $model);
        $this->assertStringNotContainsString('use '.EloquentModel::class.';', $model);
        $this->assertStringNotContainsString('extends Model implements', $model);
        $this->assertStringContainsString('use '.HasUuids::class.';', $model);
        $this->assertStringContainsString('use '.class_basename(HasUuids::class).';', $model);
        $this->assertStringContainsString('implements '.class_basename(EntityContract::class), $model);
    }

    #[Test]
    public function morph_pivot_model_extends_morph_pivot(): void
    {
        Composer::fake();

        $this->artisan($this->command, [...$this->baselineInput, '--morph-pivot' => true])
            ->assertSuccessful();

        $model = $this->app['files']->get($this->entity->filePath->toString());

        $this->assertStringContainsString('use '.MorphPivot::class.';', $model);
        $this->assertStringContainsString('extends '.class_basename(MorphPivot::class).' implements', $model);
        $this->assertStringNotContainsString('use '.EloquentModel::class.';', $model);
        $this->assertStringNotContainsString('extends Model implements', $model);
        $this->assertStringContainsString('use '.HasUuids::class.';', $model);
        $this->assertStringContainsString('use '.class_basename(HasUuids::class).';', $model);
        $this->assertStringContainsString('implements '.class_basename(EntityContract::class), $model);
    }

    #[Test]
    public function pivot_and_morph_pivot_are_mutually_exclusive(): void
    {
        Composer::fake();

        $this->artisan($this->command, [...$this->baselineInput, '--pivot' => true, '--morph-pivot' => true])
            ->assertFailed();
    }
}
