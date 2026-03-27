<?php

declare(strict_types=1);

namespace Support\Entities\Models\Console\Concerns;

use Illuminate\Console\GeneratorCommand;
use Support\Entities\Console\Concerns\RetrievesEntity;
use Support\Entities\Models\References\Model;
use Tooling\Entities\Composer\ClassMap\Collectors\Models;

/**
 * @mixin GeneratorCommand
 */
trait RetrievesModel
{
    /** @use RetrievesEntity<Model> */
    use RetrievesEntity;

    /** @return class-string<\Tooling\Composer\ClassMap\Collectors\Contracts\Collector> */
    protected function collector(): string
    {
        return Models::class;
    }

    public function resolveEntity(): void
    {
        $this->entity = Model::fromFqcn($this->retrieveEntity());
    }
}
