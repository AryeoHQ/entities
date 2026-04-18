<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Provides\HasEntity;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<ForEntityMustHaveHasEntity> */
#[CoversClass(ForEntityMustHaveHasEntity::class)]
class ForEntityMustHaveHasEntityTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new ForEntityMustHaveHasEntity;
    }

    #[Test]
    public function it_passes_when_for_entity_uses_has_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidForEntityEvent.php')], []);
    }

    #[Test]
    public function it_passes_when_class_does_not_implement_for_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/BareClass.php')], []);
    }

    #[Test]
    public function it_fails_when_for_entity_does_not_use_has_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ForEntityWithoutHasEntity.php')], [
            [
                sprintf(
                    '%s must use %s.',
                    class_basename(ForEntity::class),
                    class_basename(HasEntity::class)
                ),
                10,
            ],
        ]);
    }
}
