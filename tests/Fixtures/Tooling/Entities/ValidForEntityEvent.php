<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Support\Entities\Contracts\Entity;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Entity\IdentifiesEntity;
use Support\Entities\Events\Provides\HasEntity;

final class ValidForEntityEvent implements ForEntity
{
    use HasEntity;

    #[IdentifiesEntity]
    public readonly Entity $entity;

    public function __construct() {}
}
