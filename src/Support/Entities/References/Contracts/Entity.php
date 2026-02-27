<?php

declare(strict_types=1);

namespace Support\Entities\References\Contracts;

use Illuminate\Support\Stringable;
use Support\Entities\References\Policy;
use Support\Entities\References\Provider;

interface Entity extends Reference
{
    public Stringable $baseNamespace { get; }

    public Stringable $singular { get; }

    public Stringable $plural { get; }

    public Stringable $variableName { get; }

    public Stringable $baseDirectory { get; }

    public Stringable $relativeDirectory { get; }

    public Policy $policy { get; }

    public Provider $provider { get; }
}
