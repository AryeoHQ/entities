<?php

declare(strict_types=1);

namespace Support\Entities\Console\Concerns;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Stringable;
use Support\Entities;
use Support\Entities\References\Entity;
use Tooling\Entities\Composer\ClassMap\Collectors\Entities as EntitiesCollector;
use Tooling\GeneratorCommands\Concerns\SearchesClasses;

use function Laravel\Prompts\search;

/**
 * @template TEntity of Entities\References\Entity
 *
 * @mixin GeneratorCommand
 */
trait RetrievesEntity
{
    use RetrievesEntityFromArgument;
    use RetrievesEntityFromOption;
    use SearchesClasses;

    /** @var TEntity */
    public protected(set) Entity $entity;

    /** @return class-string<\Tooling\Composer\ClassMap\Collectors\Contracts\Collector> */
    protected function collector(): string
    {
        return EntitiesCollector::class;
    }

    public function retrieveEntity(): Stringable
    {
        $input = $this->entityFromOption() ?? $this->entityFromArgument();

        return $input !== null ? $this->qualifyEntityName($input) : $this->entityFromPrompt();
    }

    protected function qualifyEntityName(Stringable $name): Stringable
    {
        if ($name->contains('\\')) {
            return $name;
        }

        $this->components->warn('Please provide a fully-qualified class name (e.g. App\\Models\\User).');

        return $this->entityFromPrompt();
    }

    public function resolveEntity(): void
    {
        $this->entity = Entity::fromFqcn($this->retrieveEntity()); // @phpstan-ignore assign.propertyType
    }

    public function entityFromPrompt(): Stringable
    {
        return str(search(
            label: 'Which entity?',
            options: fn ($search) => $this->getClassSearchResults($search),
            required: true,
            scroll: 5,
        ));
    }
}
