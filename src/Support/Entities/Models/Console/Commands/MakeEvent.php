<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Foundation\Console\EventMakeCommand;
use Illuminate\Support\Stringable;
use Support\Entities\Console\Contracts\GeneratesForEntity;
use Support\Entities\Models\Console\Concerns\RetrievesModel;
use Support\Entities\Models\References\Event;
use Symfony\Component\Console\Input\InputOption;
use Tooling\GeneratorCommands\Concerns\GeneratorCommandCompatibility;

class MakeEvent extends EventMakeCommand implements GeneratesForEntity
{
    use GeneratorCommandCompatibility;
    use RetrievesModel;

    public Stringable $nameInput {
        get => str($this->argument('name'));
    }

    public Event $reference {
        get => $this->entity->event($this->getNameInput());
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
            '{{ domainModelSemanticEventName }}',
        ], [
            $this->entity->fqcn->ltrim('\\')->toString(),
            $this->entity->name->toString(),
            $this->reference->semanticName->toString(),
        ], GeneratorCommand::buildClass($name)); // Does not call parent::buildClass() to skip base command's operations
    }

    /** @return array<int, InputOption> */
    protected function getOptions(): array
    {
        return [
            ...$this->getEntityInputOptions(),
            new InputOption('force', 'f', InputOption::VALUE_NONE, 'Create the class even if the event already exists'),
        ];
    }
}
