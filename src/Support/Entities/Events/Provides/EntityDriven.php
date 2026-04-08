<?php

declare(strict_types=1);

namespace Support\Entities\Events\Provides;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Stringable;
use ReflectionClass;
use Support\Entities\Events\Attributes\Alias;
use Support\Entities\Events\Attributes\Exceptions\AliasMissing;
use Support\Entities\Events\Concerns\SerializesModels;

trait EntityDriven
{
    use Dispatchable;
    use SerializesModels;

    public Stringable $alias {
        get => str(
            collect((new ReflectionClass($this))->getAttributes(Alias::class))
                ->first()?->newInstance()->name ?? throw AliasMissing::on(static::class)
        );
    }

    public Stringable $uniqueAlias {
        get => str($this->alias->explode('.')->join(".{$this->entity->id}."));
    }
}
