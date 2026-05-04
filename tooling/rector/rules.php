<?php

use Tooling\Entities\Rector\ModelMustHaveBuilderMixin;
use Tooling\Entities\Rector\ModelMustHaveCollectedBy;
use Tooling\Entities\Rector\ModelMustHaveHasBuilder;
use Tooling\Entities\Rector\ModelMustHaveHasFactory;
use Tooling\Entities\Rector\ModelMustHaveHasUuids;
use Tooling\Entities\Rector\ModelMustHaveUseEloquentBuilder;
use Tooling\Entities\Rector\ModelMustHaveUseFactory;
use Tooling\Entities\Rector\ModelMustHaveUsePolicy;

return [
    ModelMustHaveBuilderMixin::class,
    ModelMustHaveCollectedBy::class,
    ModelMustHaveHasBuilder::class,
    ModelMustHaveHasFactory::class,
    ModelMustHaveHasUuids::class,
    ModelMustHaveUseEloquentBuilder::class,
    ModelMustHaveUseFactory::class,
    ModelMustHaveUsePolicy::class,
];
