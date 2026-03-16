<?php

declare(strict_types=1);

namespace Support\Entities\Models\References;

use Illuminate\Support\Stringable;
use Tooling\GeneratorCommands\References\GenericClass;

final class Event extends GenericClass
{
    public null|Stringable $subNamespace {
        get => str('Events');
    }

    public Stringable $key {
        get => str((string) $this->name)->camel();
    }

    public Model $model {
        get => Model::fromFqcn(
            $this->baseNamespace->append('\\', (string) str((string) $this->baseNamespace->afterLast('\\'))->singular()),
        );
    }

    /** The semantic event name (e.g. 'post.creating', 'post.force-deleted'). */
    public Stringable $semanticName {
        get => $this->model->variableName->append('.', (string) $this->name->kebab());
    }
}
