<?php

declare(strict_types=1);

namespace Tests\Fixtures\Support\Posts\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Support\Entities\Events\Attributes\BroadcastAs;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Provides\EntityDriven;
use Tests\Fixtures\Support\Posts\Post;

#[BroadcastAs('post.disables_model_serialization_through_interface')]
final class DisablesModelSerializationThroughInterface implements \Stringable, ForEntity
{
    use EntityDriven;

    public readonly Post $entity;

    public function __construct(Post $entity)
    {
        $this->entity = $entity;
    }

    public function __toString(): string
    {
        return self::class;
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel($this->uniqueName),
        ];
    }
}
