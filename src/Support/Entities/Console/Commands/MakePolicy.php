<?php

declare(strict_types=1);

namespace Support\Entities\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Foundation\Console\PolicyMakeCommand;
use Illuminate\Support\Stringable;
use Support\Entities\Console\Concerns\RetrievesEntity;
use Support\Entities\Console\Contracts\GeneratesForEntity;
use Support\Entities\References\Entity;
use Support\Entities\References\Policy;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tooling\GeneratorCommands\Concerns\CreatesColocatedTests;
use Tooling\GeneratorCommands\Concerns\GeneratorCommandCompatibility;

class MakePolicy extends PolicyMakeCommand implements GeneratesForEntity
{
    use CreatesColocatedTests;
    use GeneratorCommandCompatibility;

    /** @use RetrievesEntity<Entity> */
    use RetrievesEntity;

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

    /** Override parent — entity resolution replaces the model suggestion prompt. */
    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        //
    }

    protected function buildClass($name)
    {
        return str_replace([
            '{{ domainModelNamespace }}',
            '{{ domainModelName }}',
            '{{ domainModelVariableName }}',
        ], [
            $this->entity->fqcn->ltrim('\\')->toString(),
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
