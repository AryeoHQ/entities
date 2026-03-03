<?php

declare(strict_types=1);

namespace Support\Entities\References\Concerns;

use Illuminate\Support\Stringable;
use Support\Entities\References\Policy;
use Support\Entities\References\Provider;
use Tooling\Composer\Composer;
use Tooling\GeneratorCommands\References\TestClass;

trait AsEntity
{
    public null|Stringable $subdirectory = null;

    public static function fromFqcn(Stringable|string $fqcn): self
    {
        $fqcn = str($fqcn);

        return new self(
            name: class_basename($fqcn->toString()),
            baseNamespace: $fqcn->before('Entities\\'),
        );
    }

    public Stringable $singular {
        get => $this->name;
    }

    public Stringable $plural {
        get => $this->name->plural();
    }

    public Stringable $namespace {
        get => $this->baseNamespace->finish('\\')->append("Entities\\{$this->plural}");
    }

    public Stringable $fqcn {
        get => $this->namespace->append('\\', $this->name->toString());
    }

    public Stringable $variableName {
        get => $this->name->lower();
    }

    public Stringable $baseDirectory {
        get {
            $composer = resolve(Composer::class);
            $key = $this->baseNamespace->finish('\\')->toString();

            $psr4 = (array) data_get($composer->currentAsPackage->autoload, 'psr-4', []);
            $psr4Dev = (array) data_get($composer->currentAsPackage->autoloadDev, 'psr-4', []);

            $directory = str($psr4[$key] ?? $psr4Dev[$key] ?? 'app');

            return $directory->rtrim('/');
        }
    }

    public Stringable $relativeDirectory {
        get => $this->namespace->after($this->baseNamespace->toString())->replace('\\', '/');
    }

    public Stringable $directory {
        get => $this->baseDirectory->append('/', $this->relativeDirectory->toString());
    }

    public Stringable $directoryPath {
        get => resolve(Composer::class)->baseDirectory->append('/', $this->directory->toString());
    }

    public Stringable $filePath {
        get => $this->directoryPath->append('/', $this->name->toString(), '.php');
    }

    public TestClass $test {
        get => new TestClass($this);
    }

    public Policy $policy {
        get => new Policy($this);
    }

    public Provider $provider {
        get => new Provider($this);
    }
}
