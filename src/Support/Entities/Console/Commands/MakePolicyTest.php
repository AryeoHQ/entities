<?php

declare(strict_types=1);

namespace Support\Entities\Console\Commands;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Console\Concerns\RetrievesEntityTestCases;
use Tests\Support\Entities\Concerns\ProvidesEntity;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesForEntity;
use Tests\TestCase;
use Tooling\GeneratorCommands\References\Contracts\Reference;
use Tooling\GeneratorCommands\Testing\Concerns\CleansUpGeneratorCommands;
use Tooling\GeneratorCommands\Testing\Concerns\GeneratesFileTestCases;

#[CoversClass(MakePolicy::class)]
class MakePolicyTest extends TestCase implements TestsGeneratesForEntity
{
    use CleansUpGeneratorCommands;
    use GeneratesFileTestCases;
    use ProvidesEntity;
    use RetrievesEntityTestCases;

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
