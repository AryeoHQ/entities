<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<ForEntityMustHaveEntityDriven> */
#[CoversClass(ForEntityMustHaveEntityDriven::class)]
class ForEntityMustHaveEntityDrivenTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new ForEntityMustHaveEntityDriven;
    }

    #[Test]
    public function it_passes_when_for_entity_uses_entity_driven(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidEntityEvent.php')], []);
    }

    #[Test]
    public function it_passes_when_class_does_not_implement_for_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ClassWithoutEntityDriven.php')], []);
    }

    #[Test]
    public function it_fails_when_for_entity_does_not_use_entity_driven(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ForEntityWithoutEntityDriven.php')], [
            [
                'ForEntity must use EntityDriven.',
                11,
            ],
        ]);
    }
}
