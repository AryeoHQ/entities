<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Concerns;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Support\Entities\Console\Concerns\RetrievesEntity;
use Support\Entities\Models\References\Model;

/**
 * @mixin GeneratorCommand
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
        return rescue(fn () => is_a($class, EloquentModel::class, true) && ! (new \ReflectionClass($class))->isAbstract(), false, false);
    }
}
