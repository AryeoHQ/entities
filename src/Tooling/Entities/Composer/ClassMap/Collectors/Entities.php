<?php

declare(strict_types=1);

namespace Tooling\Entities\Composer\ClassMap\Collectors;

use ReflectionClass;
use Support\Entities\Contracts\Entity;
use Tooling\Composer\ClassMap\Collectors\Contracts\Collector;
use Tooling\Composer\ClassMap\Collectors\Provides\Fakeable;

class Entities implements Collector
{
    use Fakeable;

    /**
     * @param  array<class-string, \ReflectionClass<*>>  $reflections
     */
    public function collects(ReflectionClass $class, array $reflections): bool
    {
        return $class->implementsInterface(Entity::class) && ! $class->isInterface();
    }
}
