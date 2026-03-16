<?php

declare(strict_types=1);

namespace Tests\Support\Entities\References\Contracts;

use Support\Entities\References\Entity;

interface TestsEntity
{
    public Entity $subject { get; }
}
