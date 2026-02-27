<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Stringable;
use Support\Entities\Console\Concerns\CreatesColocatedTests;
use Support\Entities\Console\Concerns\GeneratorCommandCompatibility;
use Support\Entities\Console\Concerns\RetrievesEntityFromArgument;
use Support\Entities\Console\Concerns\SearchesClasses;
use Support\Entities\Console\Concerns\SearchesNamespaces;
use Support\Entities\Console\Contracts\GeneratesForEntity;
use Support\Entities\Models\Console\Concerns\ResolvesModel;
use Support\Entities\Models\References\Builder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:builder')]
class MakeBuilder extends GeneratorCommand implements GeneratesForEntity
{
    use CreatesColocatedTests;
    use GeneratorCommandCompatibility;
    use ResolvesModel;
    use RetrievesEntityFromArgument;
    use SearchesClasses;
    use SearchesNamespaces;

    protected $type = 'Model Query Builder';

    public string $stub = __DIR__.'/stubs/builder/builder.stub';

    public Stringable $nameInput {
        get => $this->reference->name;
    }

    public Builder $reference {
        get => $this->entity->builder;
    }

    public function handle()
    {
        $this->resolveEntity();

        parent::handle();

        return self::SUCCESS; // @phpstan-ignore return.type
    }

    protected function buildClass($name)
    {
        return str_replace(
            '{{ domainModelNamespace }}',
            $this->entity->fqcn->toString(),
            parent::buildClass($name),
        );
    }

    /** @return array<int, InputOption> */
    protected function getOptions(): array
    {
        return [
            new InputOption('force', 'f', InputOption::VALUE_NONE, 'Create the class even if it already exists.'),
        ];
    }
}
