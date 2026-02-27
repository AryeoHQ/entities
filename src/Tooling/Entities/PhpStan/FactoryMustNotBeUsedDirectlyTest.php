<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<FactoryMustNotBeUsedDirectly> */
#[CoversClass(FactoryMustNotBeUsedDirectly::class)]
class FactoryMustNotBeUsedDirectlyTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new FactoryMustNotBeUsedDirectly;
    }

    #[Test]
    public function it_passes_when_using_model_factory(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidFactoryUsage.php')], []);
    }

    #[Test]
    public function it_fails_when_using_factory_directly(): void
    {
        $this->analyse([$this->getFixturePath('Entities/InvalidFactoryUsage.php')], [
            [
                'Direct use of Factory is disallowed; use `Model::factory()` instead.',
                13,
            ],
            [
                'Direct use of Factory is disallowed; use `Model::factory()` instead.',
                14,
            ],
        ]);
    }
}
