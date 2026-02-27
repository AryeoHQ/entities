<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use Illuminate\Support\Stringable;
use Support\Entities\References\Concerns\RequiresEntity;
use Support\Entities\References\Contracts\Entity;
use Support\Entities\References\Contracts\Reference;

final class Event implements Reference
{
    use RequiresEntity;

    public string $event;

    public function __construct(Entity $entity, string $event)
    {
        $this->entity = $entity;
        $this->event = $event;
    }

    public Stringable $name {
        get => str(ucfirst($this->event));
    }

    public Stringable $subdirectory {
        get => str('Events');
    }

    /** The semantic event name (e.g. 'post.creating', 'post.force-deleted'). */
    public Stringable $semanticName {
        get => $this->entity->variableName->append('.')->append($this->name->kebab()->toString());
    }
}
