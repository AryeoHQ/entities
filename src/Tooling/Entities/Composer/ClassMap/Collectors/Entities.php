<?php

declare(strict_types=1);

namespace Tooling\Entities\Composer\ClassMap\Collectors;

use Illuminate\Support\Collection;
use ReflectionClass;
use Support\Entities\Contracts\Entity;
use Tooling\Composer\ClassMap\Collectors\Contracts\Collector;
use Tooling\Composer\ClassMap\Collectors\Provides\Fakeable;
use Tooling\Composer\Composer;

class Entities implements Collector
{
    use Fakeable;

    public string $key = 'entities';

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
            ->filter(fn (string $class) => $this->isEntity($class, $classMap))
            ->values();
    }

    /** @param array<class-string, non-empty-string> $classMap */
    private function isEntity(string $class, array $classMap): bool
    {
        return rescue(function () use ($class, $classMap): bool {
            $filePath = $classMap[$class] ?? null;

            if (is_string($filePath) && ! file_exists($filePath)) {
                return false;
            }

            return is_a($class, Entity::class, true) && ! (new ReflectionClass($class))->isInterface();
        }, false, false);
    }
}
