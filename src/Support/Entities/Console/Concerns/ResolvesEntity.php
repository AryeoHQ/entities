<?php

declare(strict_types=1);

namespace Support\Entities\Console\Concerns;

use Illuminate\Support\Collection;
use ReflectionClass;
use Support\Entities;
use Support\Entities\References\Entity;
use Tooling\Composer\Composer;

use function Laravel\Prompts\search;

/**
 * @template TEntity of Entities\References\Contracts\Entity
 *
 * @mixin \Illuminate\Console\GeneratorCommand
 */
trait ResolvesEntity
{
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

    protected function resolveEntity(): void
    {
        $this->entity = $this->entityFromInput() ?? $this->entityFromPrompt();
    }

    protected function entityFromInput(): null|Entity
    {
        $provided = $this->entityInput;

        if ($provided->isEmpty()) {
            return null;
        }

        if ($provided->contains('\\')) {
            return Entity::fromFqcn($provided);
        }

        $fqcn = str($this->availableNamespaces->keys()->first())
            ->finish('\\')
            ->append('Entities\\')
            ->append($provided->plural()->toString())
            ->append('\\')
            ->append($provided->singular()->toString());

        return Entity::fromFqcn($fqcn);
    }

    protected function entityFromPrompt(): Entity
    {
        $fqcn = search(
            label: 'Which entity?',
            options: fn ($search) => $this->getClassSearchResults($search),
            required: true,
            scroll: 5,
        );

        return Entity::fromFqcn($fqcn);
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
