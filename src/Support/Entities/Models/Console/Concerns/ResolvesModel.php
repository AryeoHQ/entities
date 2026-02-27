<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Concerns;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Support\Entities\Console\Concerns\ResolvesEntity;
use Support\Entities\Contracts\Entity;
use Support\Entities\Models\References\Model;

/**
 * @mixin \Illuminate\Console\GeneratorCommand
 */
trait ResolvesModel
{
    /** @use ResolvesEntity<Model> */
    use ResolvesEntity {
        ResolvesEntity::entityFromInput as baseEntityFromInput;
        ResolvesEntity::entityFromPrompt as baseEntityFromPrompt;
    }

    protected function entityFromInput(): null|Model
    {
        $base = $this->baseEntityFromInput();

        if (! $base) {
            return null;
        }

        return Model::fromFqcn($base->fqcn);
    }

    protected function entityFromPrompt(): Model
    {
        $base = $this->baseEntityFromPrompt();

        return Model::fromFqcn($base->fqcn);
    }

    protected function isSearchableEntity(string $class): bool
    {
        return is_a($class, EloquentModel::class, true) && is_a($class, Entity::class, true);
    }
}
