<?php

declare(strict_types=1);

namespace Support\Entities\Console\Concerns;

use Illuminate\Support\Stringable;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @mixin \Illuminate\Console\GeneratorCommand
 * @mixin \Support\Entities\Console\Contracts\GeneratesForEntity
 */
trait RetrievesEntityFromArgument
{
    public Stringable $entityInput {
        get => str($this->argument('entity'));
    }

    public function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        parent::afterPromptingForMissingArguments($input, $output);
    }

    /**
     * @return array<string, \Closure(): string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return array_merge(parent::promptForMissingArgumentsUsing(), [
            'entity' => fn () => $this->entityFromPrompt()->fqcn->toString(),
        ]);
    }

    /** @return array<int, InputArgument> */
    protected function getArguments(): array
    {
        return [
            new InputArgument('entity', InputArgument::REQUIRED, 'The entity FQCN (e.g. App\\Entities\\Posts\\Post).'),
        ];
    }
}
