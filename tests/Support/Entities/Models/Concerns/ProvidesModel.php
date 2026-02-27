<?php

declare(strict_types=1);

namespace Tests\Support\Entities\Models\Concerns;

use Support\Entities\Models\References\Model;

trait ProvidesModel
{
    public Model $entity {
        get => new Model(class_basename(static::class), 'App\\');
    }
}
