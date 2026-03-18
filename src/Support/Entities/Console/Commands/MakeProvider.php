<?php

declare(strict_types=1);

namespace Support\Entities\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Console\ProviderMakeCommand;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Stringable;
use Support\Entities\Console\Concerns\RetrievesEntity;
use Support\Entities\Console\Contracts\GeneratesForEntity;
use Support\Entities\References\Entity;
use Support\Entities\References\Provider;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Tooling\GeneratorCommands\Concerns\GeneratorCommandCompatibility;
use Tooling\GeneratorCommands\Concerns\SearchesClasses;

class MakeProvider extends ProviderMakeCommand implements GeneratesForEntity
{
    use GeneratorCommandCompatibility;

    /** @use RetrievesEntity<Entity> */
    use RetrievesEntity;

    use SearchesClasses;

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
        // Does not call parent::buildClass() to skip base command's operations
        $stub = GeneratorCommand::buildClass($name);

        $imports = [];
        $bootLines = [];

        if ($this->option('model')) {
            $imports[] = 'use '.Relation::class.';';
            $imports[] = 'use '.$this->entity->fqcn->ltrim('\\').';';

            $bootLines[] = class_basename(Relation::class)."::morphMap([\n"
                ."            '{$this->entity->variableName}' => {$this->entity->name}::class,\n"
                .'        ]);';
        } elseif ($this->option('policy')) {
            $imports[] = 'use '.Gate::class.';';
            $imports[] = 'use '.$this->entity->fqcn->ltrim('\\').';';
            $imports[] = 'use '.$this->entity->policy->fqcn->ltrim('\\').';';

            $bootLines[] = "Gate::policy({$this->entity->name}::class, {$this->entity->policy->name}::class);";
        } else {
            $imports[] = 'use '.$this->entity->fqcn->ltrim('\\').';';
        }

        $imports = collect($imports)->unique()->sort()->values()->implode("\n");

        $stub = str_replace('{{ imports }}', $imports."\n", $stub);
        $stub = str_replace('{{ bootBody }}', implode("\n\n        ", $bootLines), $stub);

        return $stub;
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
            new InputOption('model', null, InputOption::VALUE_NONE, 'Include morph map registration (for Eloquent models)'),
            new InputOption('policy', null, InputOption::VALUE_NEGATABLE, 'Include Gate policy registration', true),
            new InputOption('force', 'f', InputOption::VALUE_NONE, 'Create the class even if the provider already exists'),
        ];
    }
}
