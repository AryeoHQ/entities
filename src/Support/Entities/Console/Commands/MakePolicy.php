<?php

declare(strict_types=1);

namespace Support\Entities\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Foundation\Console\PolicyMakeCommand;
use Illuminate\Support\Stringable;
use Support\Entities\Console\Concerns\RetrievesEntity;
use Support\Entities\Console\Contracts\GeneratesForEntity;
use Support\Entities\References\Policy;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Tooling\GeneratorCommands\Concerns\CreatesColocatedTests;
use Tooling\GeneratorCommands\Concerns\GeneratorCommandCompatibility;
use Tooling\GeneratorCommands\Concerns\SearchesClasses;

class MakePolicy extends PolicyMakeCommand implements GeneratesForEntity
{
    use CreatesColocatedTests;
    use GeneratorCommandCompatibility;

    /** @use RetrievesEntity<\Support\Entities\References\Entity> */
    use RetrievesEntity;

    use SearchesClasses;

    public string $stub = __DIR__.'/stubs/policy/policy.stub';

    public Stringable $nameInput {
        get => $this->reference->name;
    }

    public Policy $reference {
        get => $this->entity->policy;
    }

    public function handle()
    {
        $this->resolveEntity();

        // Does not call parent::handle() to skip base command's operations
        GeneratorCommand::handle();

        return self::SUCCESS; // @phpstan-ignore return.type
    }

    protected function buildClass($name)
    {
        return str_replace([
            '{{ domainModelNamespace }}',
            '{{ domainModelName }}',
            '{{ domainModelVariableName }}',
        ], [
            $this->entity->fqcn->toString(),
            $this->entity->name->toString(),
            $this->entity->variableName->toString(),
        ], GeneratorCommand::buildClass($name)); // Does not call parent::buildClass() to skip base command's operations
    }

    /** @return array<int, InputArgument> */
    protected function getArguments(): array
    {
        return [
            ...$this->getEntityInputArguments(),
        ];
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return $this->getEntityPromptForMissingArguments();
    }

    /** @return array<int, InputOption> */
    protected function getOptions(): array
    {
        return [
            new InputOption('force', 'f', InputOption::VALUE_NONE, 'Create the class even if the policy already exists'),
            new InputOption('guard', 'g', InputOption::VALUE_OPTIONAL, 'The guard that the policy relies on'),
        ];
    }
}
