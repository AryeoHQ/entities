<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Console\Concerns\GeneratesFileTestCases;
use Support\Entities\Console\Concerns\ResolvesNamespaceTestCases;
use Support\Entities\Contracts\Entity as EntityContract;
use Support\Entities\References\Contracts\Reference;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesEntity;
use Tests\Support\Entities\Models\Concerns\ProvidesModel;
use Tests\TestCase;

#[CoversClass(MakeModel::class)]
class MakeModelTest extends TestCase implements TestsGeneratesEntity
{
    use GeneratesFileTestCases;
    use InteractsWithPublishedFiles; // @phpstan-ignore-line
    use ProvidesModel;
    use ResolvesNamespaceTestCases;

    /** @var array<array-key, string> */
    protected array $files {
        get => [
            $this->entity->directory->append('/*')->toString(),
            $this->entity->builder->directory->append('/*')->toString(),
            $this->entity->collection->directory->append('/*')->toString(),
            $this->entity->event('creating')->directory->append('/*')->toString(),
            $this->entity->factory->directory->append('/*')->toString(),
            $this->entity->policy->directory->append('/*')->toString(),
        ];
    }

    public Reference $reference {
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

    #[Test]
    public function model_related_files_are_created(): void
    {
        $this->artisan($this->command, $this->baselineInput)
            ->assertSuccessful();

        $this->assertFileExists($this->entity->filePath->toString());
        $this->assertFileExists($this->entity->factory->filePath->toString());
        $this->assertFileExists($this->entity->builder->filePath->toString());
        $this->assertFileExists($this->entity->provider->filePath->toString());
        $this->assertFileExists($this->entity->policy->filePath->toString());
        $this->assertFileExists($this->entity->policy->test->filePath->toString());
        $this->assertFileExists($this->entity->test->filePath->toString());
        $this->assertFileExists($this->entity->collection->filePath->toString());
        $this->assertFileExists($this->entity->factory->test->filePath->toString());
        $this->assertFileExists($this->entity->builder->test->filePath->toString());
        $this->assertFileExists($this->entity->collection->test->filePath->toString());
    }

    #[Test]
    public function model_contains_the_correct_attributes(): void
    {
        $this->artisan($this->command, $this->baselineInput)
            ->assertSuccessful();

        $model = file_get_contents($this->entity->filePath->toString());

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
    }

    #[Test]
    public function model_creates_and_registers_semantic_events(): void
    {
        $this->artisan($this->command, $this->baselineInput)
            ->assertSuccessful();

        $model = file_get_contents($this->entity->filePath->toString());

        $this->assertStringContainsString('protected $dispatchesEvents = [', $model);

        foreach ((new class // @phpstan-ignore class.missingExtends
        {
            use \Illuminate\Database\Eloquent\Concerns\HasEvents;
        })->getObservableEvents() as $event) {
            $ref = $this->entity->event($event);
            $this->assertStringContainsString("'{$event}' => {$ref->subdirectory}\\{$ref->name}::class,", $model);
            $this->assertFileExists($ref->filePath->toString());
        }
    }

    #[Test]
    public function no_factory_omits_factory_from_model(): void
    {
        $this->artisan($this->command, [...$this->baselineInput, '--no-factory' => true])
            ->assertSuccessful();

        $model = file_get_contents($this->entity->filePath->toString());

        $this->assertStringNotContainsString('UseFactory', $model);
        $this->assertStringNotContainsString('HasFactory', $model);
        $this->assertStringNotContainsString('Factory', $model);

        $this->assertFileDoesNotExist($this->entity->factory->filePath->toString());
        $this->assertFileDoesNotExist($this->entity->factory->test->filePath->toString());
    }

    #[Test]
    public function no_builder_omits_builder_from_model(): void
    {
        $this->artisan($this->command, [...$this->baselineInput, '--no-builder' => true])
            ->assertSuccessful();

        $model = file_get_contents($this->entity->filePath->toString());

        $this->assertStringNotContainsString('UseEloquentBuilder', $model);
        $this->assertStringNotContainsString('Builder', $model);

        $this->assertFileDoesNotExist($this->entity->builder->filePath->toString());
        $this->assertFileDoesNotExist($this->entity->builder->test->filePath->toString());
    }

    #[Test]
    public function no_collection_omits_collection_from_model(): void
    {
        $this->artisan($this->command, [...$this->baselineInput, '--no-collection' => true])
            ->assertSuccessful();

        $model = file_get_contents($this->entity->filePath->toString());

        $this->assertStringNotContainsString('CollectedBy', $model);
        $this->assertStringNotContainsString('Collection\\'.$this->entity->plural, $model);

        $this->assertFileDoesNotExist($this->entity->collection->filePath->toString());
        $this->assertFileDoesNotExist($this->entity->collection->test->filePath->toString());
    }

    #[Test]
    public function no_events_omits_events_from_model(): void
    {
        $this->artisan($this->command, [...$this->baselineInput, '--no-events' => true])
            ->assertSuccessful();

        $model = file_get_contents($this->entity->filePath->toString());

        $this->assertStringNotContainsString('Events', $model);
        $this->assertStringNotContainsString('$dispatchesEvents', $model);

        foreach ((new class // @phpstan-ignore class.missingExtends
        {
            use \Illuminate\Database\Eloquent\Concerns\HasEvents;
        })->getObservableEvents() as $event) {
            $this->assertFileDoesNotExist($this->entity->event($event)->filePath->toString());
        }
    }

    #[Test]
    public function no_policy_omits_policy_from_model(): void
    {
        $this->artisan($this->command, [...$this->baselineInput, '--no-policy' => true])
            ->assertSuccessful();

        $model = file_get_contents($this->entity->filePath->toString());

        $this->assertStringNotContainsString('UsePolicy', $model);
        $this->assertStringNotContainsString('Policy', $model);

        $this->assertFileDoesNotExist($this->entity->policy->filePath->toString());
        $this->assertFileDoesNotExist($this->entity->policy->test->filePath->toString());
    }

    #[Test]
    public function no_test_skips_test_file(): void
    {
        $this->artisan($this->command, [...$this->baselineInput, '--no-test' => true])
            ->assertSuccessful();

        $this->assertFileExists($this->entity->filePath->toString());
        $this->assertFileDoesNotExist($this->entity->test->filePath->toString());
    }
}
