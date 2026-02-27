<?php

declare(strict_types=1);

namespace Support\Entities\Events\Contracts;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Support\Entities\Contracts\Entity;

interface ForEntity extends ShouldBroadcast
{
    public Entity $entity { get; }

    public function broadcastAs(): string;
}
