<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Illuminate\Broadcasting\Channel;
use Support\Entities\Contracts\Entity;
use Support\Entities\Events\Contracts\ForEntity;

final class ForEntityWithoutEntityDriven implements ForEntity
{
    public readonly Entity $entity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function broadcastAs(): string
    {
        return 'test';
    }

    /** @return array<int, Channel> */
    public function broadcastOn(): array
    {
        return [];
    }
}
