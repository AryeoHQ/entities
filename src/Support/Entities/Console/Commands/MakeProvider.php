<?php

declare(strict_types=1);

namespace Support\Entities\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Console\ProviderMakeCommand;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Stringable;
use Support\Entities\Console\Concerns\GeneratorCommandCompatibility;
use Support\Entities\Console\Concerns\ResolvesEntity;
use Support\Entities\Console\Concerns\RetrievesEntityFromArgument;
use Support\Entities\Console\Concerns\SearchesClasses;
use Support\Entities\Console\Concerns\SearchesNamespaces;
use Support\Entities\Console\Contracts\GeneratesForEntity;
use Support\Entities\References\Provider;
use Symfony\Component\Console\Input\InputOption;

class MakeProvider extends ProviderMakeCommand implements GeneratesForEntity
{
    use GeneratorCommandCompatibility;

    /** @use ResolvesEntity<\Support\Entities\References\Entity> */
    use ResolvesEntity;

    use RetrievesEntityFromArgument;
    use SearchesClasses;
    use SearchesNamespaces;

    public string $stub = __DIR__.'/stubs/provider.stub';

    public Stringable $nameInput {
        get => $this->reference->name;
    }

    public Provider $reference {
        get => $this->entity->provider;
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
        $stub = parent::buildClass($name);

        $imports = [];
        $bootLines = [];

        if ($this->option('model')) {
            $imports[] = 'use '.Relation::class.';';
            $imports[] = "use {$this->entity->fqcn};";

            $bootLines[] = class_basename(Relation::class)."::enforceMorphMap([\n"
                ."            '{$this->entity->variableName}' => {$this->entity->name}::class,\n"
                .'        ]);';
        } elseif ($this->option('policy')) {
            $imports[] = 'use '.Gate::class.';';
            $imports[] = "use {$this->entity->fqcn};";
            $imports[] = "use {$this->entity->policy->fqcn};";

            $bootLines[] = "Gate::policy({$this->entity->name}::class, {$this->entity->policy->name}::class);";
        } else {
            $imports[] = "use {$this->entity->fqcn};";
        }

        $imports = collect($imports)->unique()->sort()->values()->implode("\n");

        $stub = str_replace('{{ imports }}', $imports."\n", $stub);
        $stub = str_replace('{{ bootBody }}', implode("\n\n        ", $bootLines), $stub);

        return $stub;
    }

    /** @return array<int, InputOption> */
    protected function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            new InputOption('model', null, InputOption::VALUE_NONE, 'Include morph map registration (for Eloquent models)'),
            new InputOption('policy', null, InputOption::VALUE_NEGATABLE, 'Include Gate policy registration', true),
        ]);
    }
}
