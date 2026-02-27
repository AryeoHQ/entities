<?php

declare(strict_types=1);

namespace Support\Entities\Console\Concerns;

use Illuminate\Support\Stringable;
use Symfony\Component\Console\Input\InputOption;

/**
 * @mixin \Illuminate\Console\GeneratorCommand
 * @mixin \Support\Entities\Console\Contracts\GeneratesForEntity
 */
trait RetrievesEntityFromOption
{
    public Stringable $entityInput {
        get => str($this->option('entity'));
    }

    /** @return array<int, InputOption> */
    protected function getEntityOptions(): array
    {
        return [
            new InputOption('entity', null, InputOption::VALUE_REQUIRED, 'The entity FQCN (e.g. App\\Entities\\Posts\\Post).'),
        ];
    }
}
