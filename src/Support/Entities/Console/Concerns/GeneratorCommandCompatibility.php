<?php

declare(strict_types=1);

namespace Support\Entities\Console\Concerns;

/**
 * @mixin \Illuminate\Console\GeneratorCommand
 * @mixin \Support\Entities\Console\Contracts\GeneratesEntity|\Support\Entities\Console\Contracts\GeneratesForEntity
 */
trait GeneratorCommandCompatibility
{
    public function getStub(): string
    {
        return $this->stub;
    }

    protected function getNameInput(): string
    {
        return $this->nameInput->toString();
    }

    protected function rootNamespace()
    {
        return $this->reference->namespace->toString();
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $this->rootNamespace();
    }

    protected function getPath($name): string
    {
        return $this->reference->directoryPath->append('/', $this->getNameInput(), '.php')->toString();
    }
}
