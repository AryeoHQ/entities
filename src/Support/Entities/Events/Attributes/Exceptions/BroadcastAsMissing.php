<?php

declare(strict_types=1);

namespace Support\Entities\Events\Attributes\Exceptions;

use RuntimeException;

final class BroadcastAsMissing extends RuntimeException
{
    public static function on(string $class): self
    {
        return new self("The [{$class}] event must have a #[BroadcastAs] attribute.");
    }
}
