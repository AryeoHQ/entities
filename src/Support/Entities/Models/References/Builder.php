<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use Illuminate\Support\Stringable;
use Support\Entities\References\Concerns\RequiresEntity;
use Support\Entities\References\Contracts\Entity;
use Tooling\GeneratorCommands\References\Contracts\Reference;

final class Builder implements Reference
{
    use RequiresEntity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public Stringable $name {
        get => str('Builder');
    }

    public Stringable $subdirectory {
        get => str('Builder');
    }
}
