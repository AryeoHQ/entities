<?php

use Tooling\Entities\Rector\ForEntityMustHaveAlias;
use Tooling\Entities\Rector\ModelMustHaveCollectedBy;
use Tooling\Entities\Rector\ModelMustHaveHasFactory;
use Tooling\Entities\Rector\ModelMustHaveHasUuids;
use Tooling\Entities\Rector\ModelMustHaveUseEloquentBuilder;
use Tooling\Entities\Rector\ModelMustHaveUseFactory;
use Tooling\Entities\Rector\ModelMustHaveUsePolicy;

return [
    ForEntityMustHaveAlias::class,
    ModelMustHaveCollectedBy::class,
    ModelMustHaveHasFactory::class,
    ModelMustHaveHasUuids::class,
    ModelMustHaveUseEloquentBuilder::class,
    ModelMustHaveUseFactory::class,
    ModelMustHaveUsePolicy::class,
];
