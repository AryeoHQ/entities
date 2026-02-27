<?php

declare(strict_types=1);

namespace Support\Entities\Console\Commands;

use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Console\Concerns\GeneratesFileTestCases;
use Support\Entities\Console\Concerns\ResolvesEntityTestCases;
use Support\Entities\References\Contracts\Reference;
use Tests\Support\Entities\Concerns\ProvidesEntity;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesForEntity;
use Tests\TestCase;

#[CoversClass(MakePolicy::class)]
class MakePolicyTest extends TestCase implements TestsGeneratesForEntity
{
    use GeneratesFileTestCases;
    use InteractsWithPublishedFiles; // @phpstan-ignore-line
    use ProvidesEntity;
    use ResolvesEntityTestCases;

    /** @var array<array-key, string> */
    protected array $files {
        get => [
            $this->entity->policy->directory->append('/*')->toString(),
        ];
    }

    public Reference $reference {
        get => $this->entity->policy;
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
    public function it_creates_a_policy_for_an_entity(): void
    {
        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $this->assertFileExists($this->reference->filePath->toString());

        $policy = file_get_contents($this->reference->filePath->toString());

        $this->assertStringContainsString("use {$this->entity->fqcn};", $policy);

        $this->assertFileExists($this->reference->test->filePath->toString());
    }
}
