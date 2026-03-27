<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Concerns;

use Illuminate\Console\GeneratorCommand;
use Support\Entities\Console\Concerns\RetrievesEntity;
use Support\Entities\Models\References\Model;

/**
 * @mixin GeneratorCommand
 */
trait RetrievesModel
{
    /** @use RetrievesEntity<Model> */
    use RetrievesEntity;

    protected function classMapCacheKey(): string
    {
        return 'models';
    }

    public function resolveEntity(): void
    {
        $this->entity = Model::fromFqcn($this->retrieveEntity());
    }
}
