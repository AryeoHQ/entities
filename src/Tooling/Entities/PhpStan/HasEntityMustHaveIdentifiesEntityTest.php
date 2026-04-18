<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Entity\IdentifiesEntity;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<HasEntityMustHaveIdentifiesEntity> */
#[CoversClass(HasEntityMustHaveIdentifiesEntity::class)]
class HasEntityMustHaveIdentifiesEntityTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new HasEntityMustHaveIdentifiesEntity;
    }

    #[Test]
    public function it_passes_when_for_entity_has_entity_attribute(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidForEntityEvent.php')], []);
    }

    #[Test]
    public function it_passes_when_class_does_not_implement_for_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/BareClass.php')], []);
    }

    #[Test]
    public function it_fails_when_for_entity_has_no_entity_attribute(): void
    {
        $this->analyse([$this->getFixturePath('Entities/HasEntityWithoutIdentifiesEntityAttribute.php')], [
            [
                sprintf(
                    '%s must have a property with the #[%s] attribute.',
                    class_basename(ForEntity::class),
                    class_basename(IdentifiesEntity::class)
                ),
                11,
            ],
        ]);
    }
}
