<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<ForEntityMustHaveBroadcastAs> */
#[CoversClass(ForEntityMustHaveBroadcastAs::class)]
class ForEntityMustHaveBroadcastAsTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new ForEntityMustHaveBroadcastAs;
    }

    #[Test]
    public function it_passes_when_for_entity_has_broadcast_as_attribute(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidEntityEvent.php')], []);
    }

    #[Test]
    public function it_passes_when_class_does_not_implement_for_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ClassWithoutEntityDriven.php')], []);
    }

    #[Test]
    public function it_fails_when_for_entity_is_missing_broadcast_as_attribute(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ForEntityWithoutBroadcastAs.php')], [
            [
                'ForEntity must have a #[BroadcastAs] attribute.',
                13,
            ],
        ]);
    }
}
