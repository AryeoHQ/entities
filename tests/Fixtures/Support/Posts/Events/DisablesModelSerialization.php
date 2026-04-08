<?php

declare(strict_types=1);

namespace Tests\Fixtures\Support\Posts\Events;

use Support\Entities\Events\Attributes\Alias;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Provides\EntityDriven;
use Tests\Fixtures\Support\Posts\Post;

#[Alias('post.disables_model_serialization')]
final class DisablesModelSerialization implements ForEntity
{
    use EntityDriven;

    public readonly Post $entity;

    public function __construct(Post $entity)
    {
        $this->entity = $entity;
    }
}
