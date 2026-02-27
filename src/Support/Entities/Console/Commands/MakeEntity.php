<?php

declare(strict_types=1);

namespace Support\Entities\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Stringable;
use Support\Entities\Console\Concerns\CreatesColocatedTests;
use Support\Entities\Console\Concerns\GeneratorCommandCompatibility;
use Support\Entities\Console\Concerns\ResolvesNamespace;
use Support\Entities\Console\Concerns\SearchesNamespaces;
use Support\Entities\Console\Contracts\GeneratesEntity;
use Support\Entities\References;
use Support\Entities\References\Contracts\Entity as EntityReference;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\confirm;

#[AsCommand(name: 'make:entity', description: 'Create a new entity.')]
class MakeEntity extends GeneratorCommand implements GeneratesEntity
{
    use CreatesColocatedTests;
    use GeneratorCommandCompatibility;
    use ResolvesNamespace;
    use SearchesNamespaces;

    public protected(set) References\Contracts\Entity $entity;

    public EntityReference $reference {
        get => $this->entity;
    }

    protected $name = null;

    protected $description = null;

    protected $type = 'Entity';

    public string $stub = __DIR__.'/stubs/entity.stub';

    public Stringable $nameInput {
        get => str($this->argument('name'));
    }

    public function handle()
    {
        if (! $this->confirmIntent()) {
            return self::SUCCESS; // @phpstan-ignore return.type
        }

        $this->prepareEntityReference();

        parent::handle();

        $this->makeProvider();
        $this->makePolicy();

        return self::SUCCESS; // @phpstan-ignore return.type
    }

    private function confirmIntent(): bool
    {
        if ((bool) $this->option('model') && $this->userIntendedModel()) {
            $this->components->warn('To create a Model, use make:model');

            return false;
        }

        return true;
    }

    private function userIntendedModel(): bool
    {
        return confirm(
            label: 'Did you intend to create a model?',
            default: true,
        );
    }

    private function prepareEntityReference(): void
    {
        $this->promptForNamespace();

        $name = str($this->getNameInput())->singular();

        $this->entity = new References\Entity(name: $name, baseNamespace: $this->baseNamespace);
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

    private function makeProvider(): void
    {
        if (! $this->option('provider')) {
            return;
        }

        $this->call(MakeProvider::class, [
            'entity' => $this->entity->fqcn->toString(),
            '--model' => false,
            '--policy' => $this->option('policy'),
            '--force' => $this->option('force'),
        ]);
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        return str_replace([
            '{{ domainModelDirectoryNamespace }}',
            '{{ domainModelName }}',
        ], [
            $this->reference->namespace->toString(),
            $this->reference->name->toString(),
        ], $stub);
    }

    /** @return array<int, InputOption> */
    protected function getOptions(): array
    {
        return [
            new InputOption('namespace', null, InputOption::VALUE_OPTIONAL, 'The root namespace for the entity'),
            new InputOption('model', 'm', InputOption::VALUE_NEGATABLE, 'Create an Eloquent model entity', true),
            new InputOption('policy', null, InputOption::VALUE_NEGATABLE, 'Create a new policy for the entity', true),
            new InputOption('provider', null, InputOption::VALUE_NEGATABLE, 'Create a new service provider for the entity', true),
            new InputOption('force', null, InputOption::VALUE_NONE, 'Create the class even if it already exists'),
        ];
    }
}
