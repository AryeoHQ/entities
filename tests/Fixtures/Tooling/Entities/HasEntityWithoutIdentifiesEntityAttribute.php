<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Support\Entities\Contracts\Entity;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Provides\HasEntity;

final class HasEntityWithoutIdentifiesEntityAttribute implements ForEntity
{
    use HasEntity;

    public readonly Entity $model;

    public function __construct() {}
}
