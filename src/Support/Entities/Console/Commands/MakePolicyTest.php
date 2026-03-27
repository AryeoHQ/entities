<?php

declare(strict_types=1);

namespace Support\Entities\Console\Commands;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\References\Entity;
use Support\Entities\References\Policy;
use Tests\Support\Entities\Concerns\ProvidesEntity;
use Tests\Support\Entities\Console\Contracts\TestsGeneratesForEntity;
use Tests\TestCase;
use Tooling\Composer\Composer;
use Tooling\Entities\Composer\ClassMap\Collectors\Entities;
use Tooling\GeneratorCommands\Testing\Concerns\GeneratesFileTestCases;

#[CoversClass(MakePolicy::class)]
class MakePolicyTest extends TestCase implements TestsGeneratesForEntity
{
    use GeneratesFileTestCases;
    use ProvidesEntity;

    public Policy $reference {
        get => $this->entity->policy;
    }

    /** @var array<string, mixed> */
    public array $baselineInput {
        get => ['entity' => $this->entity->fqcn->toString()];
    }

    #[Test]
    public function it_creates_a_policy_for_an_entity(): void
    {
        Composer::fake();

        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $this->assertTrue($this->app['files']->exists($this->reference->filePath->toString()));

        $policy = $this->app['files']->get($this->reference->filePath->toString());

        $this->assertStringContainsString('use '.$this->entity->fqcn->ltrim('\\').';', $policy);

        $this->assertTrue($this->app['files']->exists($this->reference->test->filePath->toString()));
    }

    #[Test]
    public function it_prompts_for_entity_when_argument_is_omitted(): void
    {
        $target = new Entity('PromptTarget', 'App\\');

        Composer::fake();
        Entities::fake([$target->fqcn->ltrim('\\')->toString()]);

        $this->artisan($this->command)
            ->expectsSearch('Which entity?', $target->fqcn->ltrim('\\')->toString(), 'PromptTarget', [$target->fqcn->ltrim('\\')->toString()])
            ->assertSuccessful();
    }

    #[Test]
    public function it_warns_and_prompts_when_entity_is_not_fully_qualified(): void
    {
        $target = new Entity('PromptTarget', 'App\\');

        Composer::fake();
        Entities::fake([$target->fqcn->ltrim('\\')->toString()]);

        $this->artisan($this->command, ['entity' => 'PromptTarget'])
            ->expectsOutputToContain('fully-qualified class name')
            ->expectsSearch('Which entity?', $target->fqcn->ltrim('\\')->toString(), 'PromptTarget', [$target->fqcn->ltrim('\\')->toString()])
            ->assertSuccessful();
    }
}
