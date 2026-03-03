<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Illuminate\Support\Stringable;
use Support\Entities\Console\Concerns\RetrievesEntityFromArgument;
use Support\Entities\Console\Contracts\GeneratesForEntity;
use Support\Entities\Models\Console\Concerns\ResolvesModel;
use Support\Entities\Models\References\Factory;
use Symfony\Component\Console\Input\InputOption;
use Tooling\GeneratorCommands\Concerns\CreatesColocatedTests;
use Tooling\GeneratorCommands\Concerns\GeneratorCommandCompatibility;
use Tooling\GeneratorCommands\Concerns\SearchesClasses;
use Tooling\GeneratorCommands\Concerns\SearchesNamespaces;

class MakeFactory extends FactoryMakeCommand implements GeneratesForEntity
{
    use CreatesColocatedTests;
    use GeneratorCommandCompatibility;
    use ResolvesModel;
    use RetrievesEntityFromArgument;
    use SearchesClasses;
    use SearchesNamespaces;

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

        parent::handle();

        return self::SUCCESS; // @phpstan-ignore return.type
    }

    protected function buildClass($name)
    {
        return str_replace([
            '{{ domainModelNamespace }}',
            '{{ domainModelName }}',
        ], [
            $this->entity->fqcn->toString(),
            $this->entity->name->toString(),
        ], parent::buildClass($name));
    }

    /** @return array<int, InputOption> */
    protected function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            new InputOption('force', 'f', InputOption::VALUE_NONE, 'Create the class even if it already exists.'),
        ]);
    }
}
