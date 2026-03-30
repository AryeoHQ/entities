<?php

declare(strict_types=1);

namespace Tests\Support\Entities\Concerns;

use Support\Entities\References\Entity;

trait ProvidesEntity
{
    public Entity $entity {
        get => new Entity(class_basename(static::class), 'App\\');
    }
}
