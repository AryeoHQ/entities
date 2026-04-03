<?php

declare(strict_types=1);

namespace Support\Entities\Events\Provides;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use ReflectionClass;
use Support\Entities\Events\Attributes\BroadcastAs;
use Support\Entities\Events\Attributes\Exceptions\BroadcastAsMissing;
use Support\Entities\Events\Concerns\SerializesModels;

trait EntityDriven
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    protected string $name {
        get => collect((new ReflectionClass($this))->getAttributes(BroadcastAs::class))
            ->first()?->newInstance()->name ?? throw BroadcastAsMissing::on(static::class);
    }

    protected string $uniqueName {
        get => str($this->name)->explode('.')->join(".{$this->entity->id}.");
    }

    public function broadcastAs(): string
    {
        return $this->name;
    }
}
