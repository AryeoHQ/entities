<?php

declare(strict_types=1);

namespace Support\Entities\Events\Entity\Exceptions;

use Illuminate\Support\Stringable;
use LogicException;
use Support\Entities\Contracts\Entity;
use Support\Entities\Events;
use Support\Entities\Events\Entity\IdentifiesEntity;

final class NotEntity extends LogicException
{
    private Stringable $template { get => str('[%s] property annotated with [%s] must be an [%s].'); }

    public function __construct(Events\Contracts\ForEntity $event)
    {
        parent::__construct(
            $this->template->replaceArray('%s', [$event::class, IdentifiesEntity::class, Entity::class])->toString(),
        );
    }
}
