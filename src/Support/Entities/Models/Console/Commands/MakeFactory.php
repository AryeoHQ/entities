<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Illuminate\Support\Stringable;
use Support\Entities\Console\Contracts\GeneratesForEntity;
use Support\Entities\Models\Console\Concerns\RetrievesModel;
use Support\Entities\Models\References\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Tooling\GeneratorCommands\Concerns\CreatesColocatedTests;
use Tooling\GeneratorCommands\Concerns\GeneratorCommandCompatibility;
use Tooling\GeneratorCommands\Concerns\SearchesClasses;

class MakeFactory extends FactoryMakeCommand implements GeneratesForEntity
{
    use CreatesColocatedTests;
    use GeneratorCommandCompatibility;
    use RetrievesModel;
    use SearchesClasses;

    public string $stub = __DIR__.'/stubs/factory/factory.stub';

    public Stringable $nameInput {
        get => $this->reference->name;
    }

    public Factory $reference {
        get => $this->entity->factory;
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
        ], [
            $this->entity->fqcn->ltrim('\\')->toString(),
            $this->entity->name->toString(),
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
            new InputOption('force', 'f', InputOption::VALUE_NONE, 'Create the class even if it already exists.'),
        ];
    }
}
