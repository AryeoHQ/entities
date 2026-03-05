<?php

declare(strict_types=1);

namespace Support\Entities\Console\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use ReflectionClass;
use Support\Entities;
use Support\Entities\References\Entity;
use Tooling\Composer\Composer;

use function Laravel\Prompts\search;
use function Laravel\Prompts\select;

/**
 * @template TEntity of Entities\References\Contracts\Entity
 *
 * @mixin \Illuminate\Console\GeneratorCommand
 */
trait RetrievesEntity
{
    use RetrievesEntityFromArgument;
    use RetrievesEntityFromOption;

    /** @var TEntity */
    public protected(set) Entities\References\Contracts\Entity $entity;

    /**
     * @param  Collection<int, string>  $classes
     * @return Collection<int, string>
     */
    protected function filterSearchableClasses(Collection $classes): Collection
    {
        return $classes->filter(fn (string $class) => $this->isSearchableEntity($class));
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

        $matches = $this->searchableClasses
            ->filter(fn (string $class) => str(class_basename($class))->lower()->exactly($name->lower()->toString()));

        return match ($matches->count()) {
            0 => $this->entityFromPrompt(),
            1 => str($matches->first()),
            default => str(select(
                label: "Multiple entities found matching [{$name}]:",
                options: $matches->values()->all(),
                required: true,
            )),
        };
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

    protected function isSearchableEntity(string $class): bool
    {
        $filePath = resolve(Composer::class)->classMap->get($class);

        if (is_string($filePath) && ! file_exists($filePath)) {
            return false;
        }

        return is_a($class, Entities\Contracts\Entity::class, true) && ! (new ReflectionClass($class))->isInterface();
    }
}
