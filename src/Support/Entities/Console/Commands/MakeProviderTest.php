<?php

declare(strict_types=1);

namespace Support\Entities\Console\Commands;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Console\Concerns\ResolvesEntityTestCases;
use Tests\Support\Entities\Concerns\ProvidesEntity;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesForEntity;
use Tests\TestCase;
use Tooling\GeneratorCommands\References\Contracts\Reference;
use Tooling\GeneratorCommands\Testing\Concerns\GeneratesFileTestCases;

#[CoversClass(MakeProvider::class)]
class MakeProviderTest extends TestCase implements TestsGeneratesForEntity
{
    use GeneratesFileTestCases;
    use InteractsWithPublishedFiles; // @phpstan-ignore-line
    use ProvidesEntity;
    use ResolvesEntityTestCases;

    /** @var array<array-key, string> */
    protected array $files {
        get => [
            $this->entity->provider->directory->append('/', $this->entity->provider->name->toString(), '.php')->toString(),
        ];
    }

    public Reference $reference {
        get => $this->entity->provider;
    }

    /** @var array<string, mixed> */
    public array $baselineInput {
        get => ['entity' => $this->entity->fqcn->toString()];
    }

    /** @var array<string, mixed> */
    public array $shortNameInput {
        get => ['entity' => $this->entity->name->toString()];
    }

    #[Test]
    public function it_creates_a_service_provider_for_a_model(): void
    {
        $this->artisan($this->command, [...$this->baselineInput, '--model' => true])->assertSuccessful();

        $this->assertFileExists($this->reference->filePath->toString());

        $serviceProvider = file_get_contents($this->reference->filePath->toString());

        $this->assertStringContainsString("use {$this->entity->fqcn};", $serviceProvider);
        $this->assertStringContainsString(class_basename(Relation::class).'::enforceMorphMap([', $serviceProvider);
        $this->assertStringContainsString("'".$this->entity->variableName."' => ".$this->entity->name.'::class,', $serviceProvider);
    }

    #[Test]
    public function non_model_provider_registers_policy_via_gate(): void
    {
        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $serviceProvider = file_get_contents($this->reference->filePath->toString());

        $this->assertStringNotContainsString(
            class_basename(Relation::class).'::enforceMorphMap',
            $serviceProvider
        );
        $this->assertStringContainsString('use '.Gate::class.';', $serviceProvider);
        $this->assertStringContainsString("use {$this->entity->fqcn};", $serviceProvider);
        $this->assertStringContainsString("use {$this->entity->policy->fqcn};", $serviceProvider);
        $this->assertStringContainsString(
            class_basename(Gate::class).'::policy('.$this->entity->name.'::class, Policy::class);',
            $serviceProvider
        );
    }
}
