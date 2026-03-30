<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Stringable;
use Support\Entities\Console\Commands\MakePolicy;
use Support\Entities\Console\Commands\MakeProvider;
use Support\Entities\Console\Contracts\GeneratesEntity;
use Support\Entities\Contracts\Entity as EntityContract;
use Support\Entities\Models\References\Model;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tooling\GeneratorCommands\Concerns\CreatesColocatedTests;
use Tooling\GeneratorCommands\Concerns\GeneratorCommandCompatibility;
use Tooling\GeneratorCommands\Concerns\RetrievesNamespace;

class MakeModel extends ModelMakeCommand implements GeneratesEntity
{
    use CreatesColocatedTests;
    use GeneratorCommandCompatibility;
    use RetrievesNamespace;

    protected $description = 'Create a new Model entity.';

    protected $type = 'Model';

    public string $stub = __DIR__.'/stubs/model.stub';

    public Stringable $nameInput {
        get => str($this->argument('name'));
    }

    public protected(set) Model $entity;

    public Model $reference {
        get => $this->entity;
    }

    public function handle()
    {
        if ($this->option('pivot') && $this->option('morph-pivot')) {
            $this->components->error('The --pivot and --morph-pivot options are mutually exclusive.');

            return self::FAILURE; // @phpstan-ignore return.void
        }

        $this->prepareEntityReference();

        // Does not call parent::handle() to skip base command's operations
        GeneratorCommand::handle();

        $this->makeBuilder();
        $this->makeCollection();
        $this->makeEvents();
        $this->makeFactory();
        $this->makePolicy();
        $this->makeProvider();

        return self::SUCCESS; // @phpstan-ignore return.void
    }

    protected function buildClass($name)
    {
        // Does not call parent::buildClass() to skip base command's operations
        $stub = GeneratorCommand::buildClass($name);

        $stub = str_replace([
            '{{ domainModelDirectoryNamespace }}',
            '{{ domainModelName }}',
        ], [
            $this->reference->namespace->after('\\')->toString(),
            $this->reference->name->toString(),
        ], $stub);

        $baseClassFqcn = $this->resolveBaseClass();
        $stub = str_replace('{{ baseClass }}', class_basename($baseClassFqcn), $stub);

        $imports = $this->buildModelImports();
        $attributes = $this->buildModelAttributes();
        $body = $this->buildModelBody();

        $stub = str_replace('{{ imports }}', $imports, $stub);
        $stub = str_replace('{{ body }}', $body, $stub);

        if ($attributes !== '') {
            $stub = str_replace('{{ attributes }}', $attributes, $stub);
        } else {
            $stub = str_replace("{{ attributes }}\n", '', $stub);
        }

        return $stub;
    }

    private function resolveBaseClass(): string
    {
        return match (true) {
            (bool) $this->option('morph-pivot') => MorphPivot::class,
            (bool) $this->option('pivot') => Pivot::class,
            default => EloquentModel::class,
        };
    }

    private function buildModelImports(): string
    {
        return collect([
            'use '.HasUuids::class.';',
            'use '.$this->resolveBaseClass().';',
            'use '.EntityContract::class.';',
        ])->when(
            $this->option('builder'),
            fn ($imports) => $imports
                ->push('use '.$this->entity->builder->fqcn->ltrim('\\').';')
                ->push('use '.UseEloquentBuilder::class.';')
        )->when(
            $this->option('collection'),
            fn ($imports) => $imports
                ->push('use '.$this->entity->collection->fqcn->ltrim('\\').';')
                ->push('use '.CollectedBy::class.';')
        )->when(
            $this->option('events'),
            fn ($imports) => $imports
                ->push('use '.$this->entity->event('creating')->namespace->ltrim('\\').';')
        )->when(
            $this->option('factory'),
            fn ($imports) => $imports
                ->push('use '.$this->entity->factory->fqcn->ltrim('\\').';')
                ->push('use '.UseFactory::class.';')
                ->push('use '.HasFactory::class.';')
        )->when(
            $this->option('policy'),
            fn ($imports) => $imports
                ->push('use '.$this->entity->policy->fqcn->ltrim('\\').';')
                ->push('use '.UsePolicy::class.';')
        )->sort()->values()->implode("\n");
    }

    private function buildModelAttributes(): string
    {
        return collect()->when(
            $this->option('collection'),
            fn ($attributes) => $attributes
                ->push('#['.class_basename(CollectedBy::class).'('.$this->entity->collection->name.'::class)]')
        )->when(
            $this->option('builder'),
            fn ($attributes) => $attributes
                ->push('#['.class_basename(UseEloquentBuilder::class).'('.$this->entity->builder->name.'::class)]')
        )->when(
            $this->option('factory'),
            fn ($attributes) => $attributes
                ->push('#['.class_basename(UseFactory::class).'('.$this->entity->factory->name.'::class)]')
        )->when(
            $this->option('policy'),
            fn ($attributes) => $attributes
                ->push('#['.class_basename(UsePolicy::class).'('.$this->entity->policy->name.'::class)]')
        )->implode("\n");
    }

    private function buildModelBody(): string
    {
        return collect()->when(
            $this->option('factory'),
            fn ($body) => $body
                ->push('    /** @use '.class_basename(HasFactory::class).'<'.$this->entity->factory->name.'> */')
                ->push('    use '.class_basename(HasFactory::class).';')
        )
            ->push('    use '.class_basename(HasUuids::class).';')
            ->when(
                $this->option('events'),
                fn ($body) => $body
                    ->push('')
                    ->push('    /**')
                    ->push('     * @var array<string, class-string>')
                    ->push('     */')
                    ->push('    protected $dispatchesEvents = [')
                    ->push($this->getObservableEventsString())
                    ->push('    ];'))
            ->implode("\n");
    }

    private function prepareEntityReference(): void
    {
        $this->resolveNamespace();

        $name = str($this->getNameInput())->singular();

        $this->entity = new Model(name: $name, baseNamespace: $this->baseNamespace);
    }

    private function makeFactory(): void
    {
        if (! $this->option('factory')) {
            return;
        }

        $this->call(MakeFactory::class, [
            'entity' => $this->entity->fqcn->toString(),
            '--force' => $this->option('force'),
        ]);
    }

    private function makePolicy(): void
    {
        if (! $this->option('policy')) {
            return;
        }

        $this->call(MakePolicy::class, [
            'entity' => $this->entity->fqcn->toString(),
            '--force' => $this->option('force'),
        ]);
    }

    private function makeBuilder(): void
    {
        if (! $this->option('builder')) {
            return;
        }

        $this->call(MakeBuilder::class, [
            'entity' => $this->entity->fqcn->toString(),
            '--force' => $this->option('force'),
        ]);
    }

    private function makeProvider(): void
    {
        if (! $this->option('provider')) {
            return;
        }

        $this->call(MakeProvider::class, [
            'entity' => $this->entity->fqcn->toString(),
            '--model' => true,
            '--policy' => $this->option('policy'),
            '--force' => $this->option('force'),
        ]);
    }

    private function makeCollection(): void
    {
        if (! $this->option('collection')) {
            return;
        }

        $this->call(MakeCollection::class, [
            'entity' => $this->entity->fqcn->toString(),
            '--force' => $this->option('force'),
        ]);
    }

    private function makeEvents(): void
    {
        if (! $this->option('events')) {
            return;
        }

        foreach ($this->observableEvents() as $event) {
            $this->call(MakeEvent::class, [
                'name' => $this->entity->event($event)->name,
                '--entity' => $this->entity->fqcn->toString(),
                '--force' => $this->option('force'),
            ]);
        }
    }

    /**
     * @return array<string>
     */
    protected function observableEvents(): array
    {
        return (new class // @phpstan-ignore class.missingExtends
        {
            use HasEvents;
        })->getObservableEvents();
    }

    protected function getObservableEventsString(): string
    {
        return collect($this->observableEvents())
            ->map(function (string $event) {
                $ref = $this->entity->event($event);

                return "        '{$event}' => {$ref->subNamespace}\\{$ref->name}::class,";
            })
            ->implode("\n");
    }

    /** @return array<int, InputOption> */
    protected function getOptions(): array
    {
        return [
            new InputOption('namespace', null, InputOption::VALUE_OPTIONAL, 'The root namespace for the entity'),
            new InputOption('factory', 'f', InputOption::VALUE_NEGATABLE, 'Create a new factory for the entity', true),
            new InputOption('policy', null, InputOption::VALUE_NEGATABLE, 'Create a new policy for the entity', true),
            new InputOption('builder', 'b', InputOption::VALUE_NEGATABLE, 'Create a new builder for the entity', true),
            new InputOption('collection', 'c', InputOption::VALUE_NEGATABLE, 'Create a new collection for the entity', true),
            new InputOption('events', 'e', InputOption::VALUE_NEGATABLE, 'Create semantic events for the entity', true),
            new InputOption('provider', null, InputOption::VALUE_NEGATABLE, 'Create a new service provider for the entity', true),
            new InputOption('pivot', 'p', InputOption::VALUE_NONE, 'Indicates the generated model should be a custom intermediate table model'),
            new InputOption('morph-pivot', null, InputOption::VALUE_NONE, 'Indicates the generated model should be a custom polymorphic intermediate table model'),
            new InputOption('force', null, InputOption::VALUE_NONE, 'Create the class even if it already exists'),
        ];
    }

    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        //
    }
}
