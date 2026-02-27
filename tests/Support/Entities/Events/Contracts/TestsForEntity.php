<?php

declare(strict_types=1);

namespace Tests\Support\Entities\Events\Contracts;

use Support\Entities\Events\Contracts\ForEntity;

interface TestsForEntity
{
    public ForEntity $event { get; }
}
