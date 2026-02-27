<?php

declare(strict_types=1);

namespace Support\Entities\Console\Concerns;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Support\Entities\Console\Commands\MakeTestClass;
use Symfony\Component\Console\Input\InputOption;

/**
 * @mixin \Illuminate\Console\GeneratorCommand
 * @mixin \Support\Entities\Console\Contracts\GeneratesEntity|\Support\Entities\Console\Contracts\GeneratesForEntity
 */
trait CreatesColocatedTests
{
    use CreatesMatchingTest;

    protected function addTestOptions(): void
    {
        $this->getDefinition()->addOption(new InputOption(
            'test',
            't',
            InputOption::VALUE_NEGATABLE,
            "Create a co-located test for the {$this->type}",
            true,
        ));
    }

    protected function handleTestCreation($path): bool // @phpstan-ignore missingType.parameter
    {
        if (! $this->option('test')) {
            return false;
        }

        return $this->call(MakeTestClass::class, [
            'class' => $this->reference->fqcn->toString(),
            '--force' => $this->hasOption('force') && $this->option('force'),
        ]) === self::SUCCESS;
    }
}
