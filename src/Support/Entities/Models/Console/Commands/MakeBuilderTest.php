<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use Illuminate\Database\Eloquent\Builder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Database\Eloquent\Contracts\Filterable;
use Support\Entities\Console\Concerns\RetrievesEntityTestCases;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesForEntity;
use Tests\Support\Entities\Models\Concerns\ProvidesModel;
use Tests\TestCase;
use Tooling\GeneratorCommands\References\Contracts\Reference;
use Tooling\GeneratorCommands\Testing\Concerns\CleansUpGeneratorCommands;
use Tooling\GeneratorCommands\Testing\Concerns\GeneratesFileTestCases;

#[CoversClass(MakeBuilder::class)]
class MakeBuilderTest extends TestCase implements TestsGeneratesForEntity
{
    use CleansUpGeneratorCommands;
    use GeneratesFileTestCases;
    use ProvidesModel;
    use RetrievesEntityTestCases;

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
