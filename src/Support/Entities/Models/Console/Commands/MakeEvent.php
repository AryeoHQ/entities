<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Commands;

use Illuminate\Foundation\Console\EventMakeCommand;
use Illuminate\Support\Stringable;
use Support\Entities\Console\Concerns\GeneratorCommandCompatibility;
use Support\Entities\Console\Concerns\RetrievesEntityFromOption;
use Support\Entities\Console\Concerns\SearchesClasses;
use Support\Entities\Console\Concerns\SearchesNamespaces;
use Support\Entities\Console\Contracts\GeneratesForEntity;
use Support\Entities\Models\Console\Concerns\ResolvesModel;
use Support\Entities\Models\References\Event;

class MakeEvent extends EventMakeCommand implements GeneratesForEntity
{
    use GeneratorCommandCompatibility;
    use ResolvesModel;
    use RetrievesEntityFromOption;
    use SearchesClasses;
    use SearchesNamespaces;

    public string $stub = __DIR__.'/stubs/event/event.stub';

    public Stringable $nameInput {
        get => str($this->argument('name'));
    }

    public Event $reference {
        get => $this->entity->event($this->getNameInput());
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
            '{{ domainModelSemanticEventName }}',
        ], [
            $this->entity->fqcn->toString(),
            $this->entity->name->toString(),
            $this->reference->semanticName->toString(),
        ], parent::buildClass($name));
    }

    /** @return array<int, \Symfony\Component\Console\Input\InputOption> */
    protected function getOptions(): array
    {
        return array_merge(parent::getOptions(), $this->getEntityOptions());
    }
}
