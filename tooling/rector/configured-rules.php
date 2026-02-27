<?php

use Illuminate\Database\Eloquent\Model;
use Support\Entities\Contracts\Entity;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Provides\EntityDriven;
use Tooling\Rector\Rules\AddInterfaceByClass;
use Tooling\Rector\Rules\AddTraitByInterface;

return [
    AddInterfaceByClass::class => [
        Model::class => Entity::class,
    ],
    AddTraitByInterface::class => [
        ForEntity::class => EntityDriven::class,
    ],
];
