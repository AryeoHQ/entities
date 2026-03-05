<?php

declare(strict_types=1);

namespace Tests\Support\Entities\Concerns;

use Support\Entities\Console\Commands\MakeEntity;
use Support\Entities\References\Entity;

trait ProvidesEntity
{
    public Entity $entity {
        get => new Entity(class_basename(static::class), 'App\\');
    }

    /** @var class-string */
    public string $parentCommand {
        get => MakeEntity::class;
    }

    /** @var array<string, mixed> */
    public array $parentInput {
        get => [
            'name' => $this->entity->name->toString(),
            '--namespace' => 'App\\',
            '--no-model' => true,
            '--no-policy' => true,
            '--no-provider' => true,
            '--force' => true,
        ];
    }
}
