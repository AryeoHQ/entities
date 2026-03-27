<?php

declare(strict_types=1);

namespace Tooling\Entities\Composer\ClassMap\Collectors;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ReflectionClass;
use Tooling\Composer\ClassMap\Collectors\Contracts\Collector;
use Tooling\Composer\ClassMap\Collectors\Provides\Fakeable;
use Tooling\Composer\Composer;

class Models implements Collector
{
    use Fakeable;

    public string $key = 'models';

    /** @return \Illuminate\Support\Collection<int, string> */
    public function collect(Composer $composer): Collection
    {
        $classMap = $composer->sourcePsr4ClassMap;

        $namespaces = $composer->currentAsPackage->psr4Mappings
            ->map(fn (\Tooling\Composer\Packages\Psr4Mapping $mapping): string => $mapping->prefix->toString())
            ->unique();

        return collect($classMap)
            ->keys()
            ->filter(fn (string $class) => $namespaces->contains(
                fn (string $namespace) => str_starts_with('\\'.$class, $namespace)
            ))
            ->filter(fn (string $class) => rescue(
                fn () => is_a($class, Model::class, true) && ! (new ReflectionClass($class))->isAbstract(),
                false,
                false,
            ))
            ->values();
    }
}
