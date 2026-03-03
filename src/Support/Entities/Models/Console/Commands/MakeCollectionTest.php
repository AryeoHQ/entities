<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use Illuminate\Database\Eloquent\Collection;
use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Console\Concerns\ResolvesEntityTestCases;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesForEntity;
use Tests\Support\Entities\Models\Concerns\ProvidesModel;
use Tests\TestCase;
use Tooling\GeneratorCommands\References\Contracts\Reference;
use Tooling\GeneratorCommands\Testing\Concerns\GeneratesFileTestCases;

#[CoversClass(MakeCollection::class)]
class MakeCollectionTest extends TestCase implements TestsGeneratesForEntity
{
    use GeneratesFileTestCases;
    use InteractsWithPublishedFiles; // @phpstan-ignore-line
    use ProvidesModel;
    use ResolvesEntityTestCases;

    /** @var array<array-key, string> */
    protected array $files {
        get => [
            $this->entity->collection->directory->append('/*')->toString(),
        ];
    }

    public Reference $reference {
        get => $this->entity->collection;
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
    public function it_creates_a_collection_for_an_entity(): void
    {
        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $this->assertFileExists($this->reference->filePath->toString());

        $collection = file_get_contents($this->reference->filePath->toString());

        $this->assertStringContainsString('use '.Collection::class.';', $collection);
        $this->assertStringContainsString('@extends '.class_basename(Collection::class).'<int, \\'.$this->entity->fqcn.'>', $collection);
        $this->assertStringContainsString('class '.$this->reference->name.' extends '.class_basename(Collection::class), $collection);

        $this->assertFileExists($this->reference->test->filePath->toString());
    }
}
