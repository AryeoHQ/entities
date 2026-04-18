<?php

declare(strict_types=1);

namespace Support\Entities\Events\Entity\Exceptions;

use Illuminate\Support\Stringable;
use LogicException;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Entity\IdentifiesEntity;

final class NotDefined extends LogicException
{
    private Stringable $template { get => str('[%s] is missing a property annotated with [%s].'); }

    public function __construct(ForEntity $event)
    {
        parent::__construct(
            $this->template->replaceArray('%s', [$event::class, IdentifiesEntity::class])->toString(),
        );
    }
}
