<?php

declare(strict_types=1);

namespace Tooling\Entities\Composer\ClassMap\Collectors;

use Illuminate\Support\Collection;
use ReflectionClass;
use Support\Entities\Contracts\Entity;
use Tooling\Composer\ClassMap\Collectors\Contracts\Collector;
use Tooling\Composer\ClassMap\Collectors\Provides\Fakeable;

class Entities implements Collector
{
    use Fakeable;

    /** @return \Illuminate\Support\Collection<int, class-string> */
    public function collect(Collection $classes): Collection
    {
        return $classes
            ->filter(fn (string $class) => $this->isEntity($class))
            ->values();
    }

    private function isEntity(string $class): bool
    {
        return rescue(
            fn () => is_a($class, Entity::class, true) && ! (new ReflectionClass($class))->isInterface(),
            false,
            false,
        );
    }
}
