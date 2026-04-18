<?php

declare(strict_types=1);

namespace Tests\Fixtures\Support\Posts\Events;

use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Entity\IdentifiesEntity;
use Support\Entities\Events\Provides\HasEntity;
use Tests\Fixtures\Support\Posts\Post;

final class Created implements ForEntity
{
    use HasEntity;

    #[IdentifiesEntity]
    public readonly Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
