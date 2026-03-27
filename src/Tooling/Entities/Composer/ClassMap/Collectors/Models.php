<?php

declare(strict_types=1);

namespace Tooling\Entities\Composer\ClassMap\Collectors;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ReflectionClass;
use Tooling\Composer\ClassMap\Collectors\Contracts\Collector;
use Tooling\Composer\ClassMap\Collectors\Provides\Fakeable;

class Models implements Collector
{
    use Fakeable;

    /** @return \Illuminate\Support\Collection<int, class-string> */
    public function collect(Collection $classes): Collection
    {
        return $classes
            ->filter(fn (string $class) => rescue(
                fn () => is_a($class, Model::class, true) && ! (new ReflectionClass($class))->isAbstract(),
                false,
                false,
            ))
            ->values();
    }
}
