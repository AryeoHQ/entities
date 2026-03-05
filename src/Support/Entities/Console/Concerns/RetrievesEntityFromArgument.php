<?php

declare(strict_types=1);

namespace Support\Entities\Console\Concerns;

use Illuminate\Support\Stringable;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \Illuminate\Console\GeneratorCommand
 */
trait RetrievesEntityFromArgument
{
    protected function entityFromArgument(): null|Stringable
    {
        if (! $this->hasArgument('entity')) {
            return null;
        }

        $provided = str($this->argument('entity')); // @phpstan-ignore argument.type, larastan.console.undefinedArgument

        if ($provided->isEmpty()) {
            return null;
        }

        return $provided;
    }

    /** @return array<int, InputArgument> */
    protected function getEntityInputArguments(): array
    {
        return [
            new InputArgument('entity', InputArgument::REQUIRED, 'The entity FQCN (e.g. App\\Entities\\Posts\\Post).'),
        ];
    }

    /**
     * @return array<string, \Closure(): string>
     */
    protected function getEntityPromptForMissingArguments(): array
    {
        return ['entity' => fn () => $this->entityFromPrompt()->toString()];
    }
}
