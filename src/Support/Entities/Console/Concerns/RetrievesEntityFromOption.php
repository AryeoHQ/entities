<?php

declare(strict_types=1);

namespace Support\Entities\Console\Concerns;

use Illuminate\Support\Stringable;
use Symfony\Component\Console\Input\InputOption;

/**
 * @mixin \Illuminate\Console\GeneratorCommand
 */
trait RetrievesEntityFromOption
{
    protected function entityFromOption(): null|Stringable
    {
        if (! $this->hasOption('entity')) {
            return null;
        }

        $provided = str($this->option('entity')); // @phpstan-ignore argument.type, larastan.console.undefinedOption

        if ($provided->isEmpty()) {
            return null;
        }

        return $provided;
    }

    /** @return array<int, InputOption> */
    protected function getEntityInputOptions(): array
    {
        return [
            new InputOption('entity', null, InputOption::VALUE_REQUIRED, 'The entity FQCN (e.g. App\\Entities\\Posts\\Post).'),
        ];
    }
}
