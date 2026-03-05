<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Concerns;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Support\Entities\Console\Concerns\RetrievesEntity;
use Support\Entities\Contracts\Entity;
use Support\Entities\Models\References\Model;

/**
 * @mixin \Illuminate\Console\GeneratorCommand
 */
trait RetrievesModel
{
    /** @use RetrievesEntity<Model> */
    use RetrievesEntity;

    public function resolveEntity(): void
    {
        $this->entity = Model::fromFqcn($this->retrieveEntity());
    }

    protected function isSearchableEntity(string $class): bool
    {
        return is_a($class, EloquentModel::class, true) && is_a($class, Entity::class, true);
    }
}
