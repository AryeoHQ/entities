<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use Illuminate\Database\Eloquent\Builder;
use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Database\Eloquent\Contracts\Filterable;
use Support\Entities\Console\Concerns\RetrievesEntityTestCases;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesForEntity;
use Tests\Support\Entities\Models\Concerns\ProvidesModel;
use Tests\TestCase;
use Tooling\GeneratorCommands\References\Contracts\Reference;
use Tooling\GeneratorCommands\Testing\Concerns\GeneratesFileTestCases;

#[CoversClass(MakeBuilder::class)]
class MakeBuilderTest extends TestCase implements TestsGeneratesForEntity
{
    use GeneratesFileTestCases;
    use InteractsWithPublishedFiles; // @phpstan-ignore-line
    use ProvidesModel;
    use RetrievesEntityTestCases;

    /** @var array<array-key, string> */
    protected array $files {
        get => [
            $this->entity->builder->directory->append('/*')->toString(),
        ];
    }

    public Reference $reference {
        get => $this->entity->builder;
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
    public function it_creates_a_builder_for_an_entity(): void
    {
        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $this->assertFileExists($this->reference->filePath->toString());

        $builder = file_get_contents($this->reference->filePath->toString());

        $this->assertStringContainsString('extends \\'.Builder::class, $builder);
        $this->assertStringContainsString('implements '.class_basename(Filterable::class), $builder);

        $this->assertFileExists($this->reference->test->filePath->toString());
    }
}
