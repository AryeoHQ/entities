<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\IdentifiesEntity\IdentifiesEntity;
use Support\Entities\Events\Provides\HasEntity;

final class HasEntityWithInvalidIdentifiesEntityPromotedInvalidType implements ForEntity
{
    use HasEntity;

    public function __construct(
        #[IdentifiesEntity]
        public readonly string $name,
    ) {}
}
