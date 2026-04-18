<?php

declare(strict_types=1);

namespace Support\Entities\Events\Entity\Exceptions;

use Illuminate\Support\Stringable;
use LogicException;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Entity\IdentifiesEntity;

final class MultipleDefined extends LogicException
{
    private Stringable $template { get => str('[%s] can only have one property annotated with [%s].'); }

    public function __construct(ForEntity $event)
    {
        parent::__construct(
            $this->template->replaceArray('%s', [class_basename($event::class), class_basename(IdentifiesEntity::class)])->toString(),
        );
    }
}
