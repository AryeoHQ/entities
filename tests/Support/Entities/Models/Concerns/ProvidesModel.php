<?php

declare(strict_types=1);

namespace Tests\Support\Entities\Models\Concerns;

use Support\Entities\Models\Console\Commands\MakeModel;
use Support\Entities\Models\References\Model;

trait ProvidesModel
{
    public Model $entity {
        get => new Model(class_basename(static::class), 'App\\');
    }

    /** @var class-string */
    public string $parentCommand {
        get => MakeModel::class;
    }

    /** @var array<string, mixed> */
    public array $parentInput {
        get => [
            'name' => $this->entity->name->toString(),
            '--namespace' => 'App\\',
            '--no-factory' => true,
            '--no-policy' => true,
            '--no-builder' => true,
            '--no-collection' => true,
            '--no-events' => true,
            '--no-provider' => true,
            '--force' => true,
        ];
    }
}
