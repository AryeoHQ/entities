<?php

declare(strict_types=1);

namespace Support\Entities\Console\Concerns;

use PHPUnit\Framework\Attributes\Test;

/**
 * @mixin \Tests\TestCase
 */
trait ResolvesEntityTestCases
{
    #[Test]
    public function it_resolves_the_entity_from_a_fqcn(): void
    {
        $this->artisan($this->command, $this->baselineInput)->assertSuccessful();

        $this->assertFileExists($this->expectedFilePath);
    }

    #[Test]
    public function it_resolves_the_entity_from_a_short_name(): void
    {
        $this->artisan($this->command, $this->shortNameInput)->assertSuccessful();

        $this->assertFileExists($this->expectedFilePath);
    }
}
