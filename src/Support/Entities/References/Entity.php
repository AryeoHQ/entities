<?php

declare(strict_types=1);

namespace Support\Entities\References;

use Illuminate\Support\Stringable;
use Support\Entities\References\Concerns\AsEntity;
use Support\Entities\References\Contracts\Entity as EntityContract;

final class Entity implements EntityContract
{
    use AsEntity;

    public Stringable $name;

    public Stringable $baseNamespace;

    public function __construct(Stringable|string $name, Stringable|string $baseNamespace)
    {
        $this->name = str($name);
        $this->baseNamespace = str($baseNamespace);
    }
}
